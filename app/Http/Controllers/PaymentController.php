<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Order;
use App\Services\NotificationService;

class PaymentController extends Controller
{
    private function setupMidtrans(): void
    {
        \Midtrans\Config::$serverKey    = trim(config('midtrans.server_key'));
        \Midtrans\Config::$clientKey    = trim(config('midtrans.client_key'));
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        \Midtrans\Config::$curlOptions = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_TIMEOUT        => 60,
        ];
    }

    /* ===============================
       CHECKOUT — buat Snap token
    =============================== */
    public function checkout(Request $request)
    {
        $this->setupMidtrans();

        $request->validate([
            'order_id'       => 'required|exists:orders,order_id',
            'payment_filter' => 'nullable|string|in:ewallet,bank,credit',
        ]);

        $order = Order::with(['service', 'client', 'artist'])
            ->where('order_id', $request->order_id)
            ->where('client_id', Auth::user()->user_id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json(['error' => 'Order ini sudah dibayar.'], 422);
        }

        if (empty(config('midtrans.server_key'))) {
            return response()->json(['error' => 'Server key kosong. Cek .env MIDTRANS_SERVER_KEY.'], 500);
        }

        Log::info('Midtrans checkout attempt', [
            'order_id'       => $order->order_id,
            'total_price'    => $order->total_price,
            'payment_filter' => $request->payment_filter,
            'server_key'     => substr(config('midtrans.server_key'), 0, 15) . '...',
        ]);

        $itemDetails = $this->buildItemDetails($order);
        $grossAmount = (int) $order->total_price;

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_id,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->client->name ?? 'Client',
                'email'      => $order->client->email ?? 'noemail@example.com',
            ],
            'item_details' => $itemDetails,
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit'       => 'hour',
                'duration'   => 24,
            ],
        ];

        // Filter metode pembayaran di Snap popup (tidak ubah logika token)
        $enabledPayments = $this->getEnabledPayments($request->payment_filter);
        if (!empty($enabledPayments)) {
            $params['enabled_payments'] = $enabledPayments;
        }

        Log::info('Midtrans item details', [
            'items'        => $itemDetails,
            'gross_amount' => $grossAmount,
            'filter'       => $request->payment_filter,
        ]);

        try {
            $snapToken = @\Midtrans\Snap::getSnapToken($params);

            if (empty($snapToken)) {
                throw new \Exception('Snap token kosong');
            }

            Log::info('Midtrans snap token OK', [
                'order_id'   => $order->order_id,
                'snap_token' => substr($snapToken, 0, 20) . '...',
            ]);

            // JANGAN simpan snap_token ke DB — kolom tidak ada di tabel
            return response()->json([
                'snap_token' => $snapToken,
                'order_id'   => $order->order_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap error', [
                'message'  => $e->getMessage(),
                'order_id' => $order->order_id,
            ]);

            return response()->json([
                'error' => 'Gagal membuat sesi pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ===============================
       NOTIFICATION — webhook Midtrans
       Kirim notif ke artist saat pertama kali paid
    =============================== */
    public function notification(Request $request)
    {
        $this->setupMidtrans();

        try {
            $payload = $request->all();

            Log::info('Midtrans RAW Notification', $payload);

            $orderId           = $payload['order_id'] ?? null;
            $transactionStatus = $payload['transaction_status'] ?? null;
            $fraudStatus       = $payload['fraud_status'] ?? null;
            $paymentType       = $payload['payment_type'] ?? null;

            if (!$orderId) {
                return response()->json(['message' => 'Order ID missing'], 400);
            }

            if (!Str::isUuid($orderId)) {
                Log::warning('Invalid UUID order_id from Midtrans: ' . $orderId);
                return response()->json(['message' => 'Invalid order_id format'], 200);
            }

            $order = Order::with(['service', 'client'])
                ->where('order_id', $orderId)
                ->first();

            if (!$order) {
                Log::warning('Order not found: ' . $orderId);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Simpan status sebelum update untuk cek apakah baru pertama paid
            $wasPaid = $order->payment_status === 'paid';

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $order->payment_status = 'paid';
                    $order->status         = 'paid';
                    $order->paid_at        = now();
                }
            } elseif ($transactionStatus === 'settlement') {
                $order->payment_status = 'paid';
                $order->status         = 'paid';
                $order->paid_at        = now();
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $order->payment_status = 'failed';
            } elseif ($transactionStatus === 'pending') {
                $order->payment_status = 'unpaid';
            } elseif ($transactionStatus === 'refund') {
                $order->payment_status = 'refunded';
                $order->status         = 'canceled';
            }

            if ($paymentType) {
                $order->payment_method = $paymentType;
            }

            $order->save();

            // Kirim notif ke artist hanya saat pertama kali paid
            if (!$wasPaid && $order->payment_status === 'paid') {
                NotificationService::orderPaid(
                    $order->artist_id,
                    $order->order_id,
                    $order->client->name ?? 'Client',
                    $order->service->title ?? 'Commission'
                );

                Log::info('Notifikasi order paid terkirim ke artist', [
                    'artist_id' => $order->artist_id,
                    'order_id'  => $order->order_id,
                ]);
            }

            return response()->json(['message' => 'OK'], 200);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /* ===============================
       SUCCESS / FINISH / UNFINISH / ERROR
    =============================== */
    public function success(Request $request)
    {
        $orderId = $request->order_id;
        $order   = Order::where('order_id', $orderId)
            ->where('client_id', Auth::user()->user_id)
            ->first();

        if ($order && $order->payment_status === 'paid') {
            return redirect()->route('order.detail', $orderId)
                ->with('success', 'Pembayaran berhasil! Order sedang diproses.');
        }

        return redirect()->route('cart.index')
            ->with('info', 'Pembayaran sedang diverifikasi.');
    }

    public function finish(Request $request)
    {
        return redirect()->route('cart.index', ['tab' => 'status'])
            ->with('success', 'Transaksi selesai.');
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('cart.index')
            ->with('info', 'Pembayaran belum selesai. Silakan coba lagi.');
    }

    public function error(Request $request)
    {
        return redirect()->route('cart.index')
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }

    /* ===============================
       VERIFY STATUS — dipanggil dari cart onSuccess
       Midtrans sandbox kadang webhook lambat,
       jadi client langsung update via endpoint ini
    =============================== */
    public function verifyStatus(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:orders,order_id']);

        $order = Order::with(['service', 'client'])
            ->where('order_id', $request->order_id)
            ->where('client_id', Auth::user()->user_id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json(['is_paid' => true]);
        }

        $wasPaid = false;

        $order->update([
            'payment_status' => 'paid',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        // Kirim notif ke artist jika belum terkirim via webhook
        if (!$wasPaid) {
            NotificationService::orderPaid(
                $order->artist_id,
                $order->order_id,
                $order->client->name ?? 'Client',
                $order->service->title ?? 'Commission'
            );
        }

        return response()->json(['is_paid' => true]);
    }

    /* ===============================
       PRIVATE: build item details (tanpa fee/tax)
    =============================== */
    private function buildItemDetails(Order $order): array
    {
        $items = [];

        $basePrice = (int) ($order->subtotal_price ?? $order->total_price);

        $items[] = [
            'id'       => $order->service_id ?? 'commission',
            'price'    => $basePrice,
            'quantity' => 1,
            'name'     => substr($order->service->title ?? 'Commission', 0, 50),
        ];

        // Add-ons — hanya array [{name, price}], bukan reference image path
        if (!empty($order->selected_addons)) {
            $addons = is_array($order->selected_addons)
                ? $order->selected_addons
                : (json_decode($order->selected_addons, true) ?? []);

            foreach ($addons as $addon) {
                if (
                    is_array($addon)
                    && isset($addon['price'], $addon['name'])
                    && ($addon['price'] ?? 0) > 0
                ) {
                    $items[] = [
                        'id'       => 'addon-' . Str::slug($addon['name']),
                        'price'    => (int) $addon['price'],
                        'quantity' => 1,
                        'name'     => substr($addon['name'], 0, 50),
                    ];
                }
            }
        }

        // Pastikan total = gross_amount (Midtrans strict)
        $itemTotal = array_sum(array_column($items, 'price'));
        $gross     = (int) $order->total_price;

        if ($itemTotal !== $gross) {
            $items[0]['price'] += ($gross - $itemTotal);
        }

        return $items;
    }

    /* ===============================
       PRIVATE: filter metode pembayaran Snap
    =============================== */
    private function getEnabledPayments(?string $filter): array
    {
        return match ($filter) {
            'ewallet' => ['gopay', 'shopeepay', 'other_qris'],
            'bank'    => ['bca_va', 'bni_va', 'bri_va', 'mandiri_bill', 'permata_va', 'other_va'],
            'credit'  => ['credit_card'],
            default   => [],
        };
    }
}
