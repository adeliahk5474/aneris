<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Order;
use App\Models\CommissionService;
use App\Services\NotificationService;

class OrderController extends Controller
{
    /* ===============================
       STORE
    =============================== */
    public function store(Request $request)
    {
        $request->validate([
            'service_id'      => 'required|exists:commission_services,service_id',
            'note'            => 'nullable|string|max:2000',
            'selected_addons' => 'nullable|string',
        ]);

        $service = CommissionService::where('service_id', $request->service_id)->firstOrFail();

        $existing = Order::where('client_id', Auth::user()->user_id)
            ->where('service_id', $request->service_id)
            ->where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->first();

        if ($existing) {
            return redirect()
                ->route('cart.index', ['tab' => 'checkout', 'order_id' => $existing->order_id])
                ->with('info', 'Kamu sudah punya order untuk jasa ini. Selesaikan pembayarannya dulu ya!');
        }

        $subtotal       = (float) $service->base_price;
        $selectedAddons = [];

        if ($request->filled('selected_addons')) {
            $decoded = json_decode($request->selected_addons, true);
            if (is_array($decoded)) {
                foreach ($decoded as $a) {
                    $name  = trim($a['name']  ?? '');
                    $price = (float) ($a['price'] ?? 0);
                    if ($name !== '') {
                        $selectedAddons[] = ['name' => $name, 'price' => $price];
                        $subtotal += $price;
                    }
                }
            }
        }

        $order = Order::create([
            'order_id'        => Str::uuid(),
            'service_id'      => $service->service_id,
            'client_id'       => Auth::user()->user_id,
            'artist_id'       => $service->artist_id,
            'note'            => $request->note ?? null,
            'selected_addons' => !empty($selectedAddons) ? $selectedAddons : null,
            'payment_method'  => 'midtrans',
            'payment_status'  => 'unpaid',
            'subtotal_price'  => $subtotal,
            'total_price'     => $subtotal,
            'status'          => 'pending',
            'revision_limit'  => $service->max_revisions ?? 2,
        ]);

        return redirect()
            ->route('cart.index', ['tab' => 'checkout', 'order_id' => $order->order_id])
            ->with('success', 'Order berhasil dibuat! Lengkapi brief dan lanjutkan ke pembayaran.');
    }

    /* ===============================
       DETAIL
    =============================== */
    public function detail($orderId)
    {
        $userId = Auth::user()->user_id;

        $order = Order::with(['service', 'service.category', 'client', 'artist'])
            ->where('order_id', $orderId)
            ->where(function ($q) use ($userId) {
                $q->where('client_id', $userId)
                    ->orWhere('artist_id', $userId);
            })
            ->firstOrFail();

        // Refresh late status setiap kali halaman dibuka
        $order->refreshLateStatus();

        $isArtist = $userId === $order->artist_id;
        $isClient = $userId === $order->client_id;

        $phases = [
            'sketch'    => ['label' => 'Sketch',    'icon' => 'bi-pencil-fill',       'idx' => 0],
            'coloring'  => ['label' => 'Coloring',  'icon' => 'bi-palette-fill',      'idx' => 1],
            'rendering' => ['label' => 'Rendering', 'icon' => 'bi-layers-fill',       'idx' => 2],
            'final'     => ['label' => 'Final',     'icon' => 'bi-check-circle-fill', 'idx' => 3],
        ];
        $currentPhaseIdx = $order->status === 'completed'
            ? count($phases)
            : ($phases[$order->phase ?? 'sketch']['idx'] ?? 0);

        $statusColors = [
            'pending'            => ['bg' => 'rgba(250,204,21,.12)',  'color' => '#facc15', 'label' => 'Pending'],
            'paid'               => ['bg' => 'rgba(34,197,94,.12)',   'color' => '#22c55e', 'label' => 'Paid'],
            'in_progress'        => ['bg' => 'rgba(139,92,246,.15)',  'color' => '#8b5cf6', 'label' => 'In Progress'],
            'revision_requested' => ['bg' => 'rgba(250,204,21,.12)',  'color' => '#facc15', 'label' => 'Revision Requested'],
            'revision'           => ['bg' => 'rgba(239,68,68,.12)',   'color' => '#ef4444', 'label' => 'Revision'],
            'waiting_client'     => ['bg' => 'rgba(56,189,248,.12)',  'color' => '#38bdf8', 'label' => 'Waiting Review'],
            'completed'          => ['bg' => 'rgba(34,197,94,.12)',   'color' => '#22c55e', 'label' => 'Completed'],
            'canceled'           => ['bg' => 'rgba(239,68,68,.1)',    'color' => '#f87171', 'label' => 'Canceled'],
        ];
        $sc = $statusColors[$order->status] ?? $statusColors['pending'];

        $lateColors = [
            'late'    => ['bg' => 'rgba(250,204,21,.12)', 'color' => '#facc15', 'icon' => 'bi-clock-fill',                'label' => 'Late'],
            'overdue' => ['bg' => 'rgba(249,115,22,.12)', 'color' => '#f97316', 'icon' => 'bi-exclamation-triangle-fill', 'label' => 'Overdue'],
            'delayed' => ['bg' => 'rgba(239,68,68,.12)',  'color' => '#ef4444', 'icon' => 'bi-x-octagon-fill',            'label' => 'Severely Delayed'],
        ];
        $lc = $order->late_status ? ($lateColors[$order->late_status] ?? null) : null;

        $revisionsLeft   = $order->revisionsLeft();
        $revisionPercent = $order->revision_limit > 0
            ? round(($order->revision_count / $order->revision_limit) * 100)
            : 0;

        $queuePosition = Order::where('service_id', $order->service_id)
            ->whereNotIn('status', ['completed', 'canceled'])
            ->where('created_at', '<=', $order->created_at)
            ->count();
        $totalQueue = Order::where('service_id', $order->service_id)
            ->whereNotIn('status', ['completed', 'canceled'])
            ->count();

        $myReview  = $order->reviews()->where('reviewer_id', $userId)->first();
        $canReview = $order->status === 'completed' && !$myReview;

        return view('commission.order-detail', compact(
            'order',
            'isArtist',
            'isClient',
            'phases',
            'currentPhaseIdx',
            'sc',
            'lc',
            'revisionsLeft',
            'revisionPercent',
            'queuePosition',
            'totalQueue',
            'myReview',
            'canReview'
        ));
    }

    /* ===============================
       UPDATE BRIEF
    =============================== */
    public function updateBrief(Request $request, $orderId)
    {
        $request->validate([
            'note'               => 'nullable|string|max:3000',
            'reference_images'   => 'nullable|array|max:4',
            'reference_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'existing_refs'      => 'nullable|array',
        ]);

        $order = Order::where('order_id', $orderId)
            ->where('client_id', Auth::user()->user_id)
            ->firstOrFail();

        if (in_array($order->status, ['in_progress', 'waiting_client', 'completed', 'canceled'])) {
            return back()->with('error', 'Brief tidak bisa diubah setelah order diproses.');
        }

        $refs = array_values(array_filter(
            $request->input('existing_refs', []),
            fn($r) => !empty($r)
        ));

        if ($request->hasFile('reference_images')) {
            foreach ($request->file('reference_images') as $file) {
                if (count($refs) >= 4) break;
                $refs[] = $file->store('orders/references', 'public');
            }
        }

        $order->update([
            'note'            => $request->note,
            'selected_addons' => !empty($refs) ? $refs : null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Brief berhasil disimpan!');
    }

    /* ===============================
       CART
    =============================== */
    public function cart()
    {
        $orders = Order::with(['artist', 'service'])
            ->where('client_id', Auth::user()->user_id)
            ->latest()
            ->get();

        $focusOrderId = request('order_id');

        $pendingOrders = $orders
            ->where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->map(function ($order) {
                $order->noteCharCount = strlen($order->note ?? '');
                return $order;
            });

        $inProgressOrders = $orders->filter(function ($o) {
            if ($o->status === 'canceled') return false;
            if ($o->status === 'pending' && $o->payment_status === 'unpaid') return false;
            return true;
        });

        $activeTab = request('tab', 'checkout');
        if (!in_array($activeTab, ['checkout', 'status'])) {
            $activeTab = 'checkout';
        }

        $grand = $pendingOrders->sum('total_price');

        $statusOrders = $orders->filter(function ($o) {
            if ($o->status === 'canceled') return false;
            if ($o->status === 'pending' && $o->payment_status === 'unpaid') return false;
            return true;
        })->map(function ($order) {
            $order->progressStepCurrent = match ($order->status) {
                'paid'                           => 0,
                'in_progress'                    => $order->phase === 'coloring' ? 2 : 1,
                'revision_requested', 'revision' => $order->phase === 'coloring' ? 2 : 1,
                'waiting_client'                 => $order->phase === 'final' ? 4 : ($order->phase === 'coloring' ? 3 : 2),
                'completed'                      => 5,
                default                          => 1,
            };
            return $order;
        });

        $progressSteps = ['Ordered', 'Sketching', 'Coloring', 'Final', 'Done'];

        return view('pages.cart', compact(
            'orders',
            'focusOrderId',
            'pendingOrders',
            'inProgressOrders',
            'activeTab',
            'grand',
            'statusOrders',
            'progressSteps'
        ));
    }

    /* ===============================
       ACCEPT
       Artist : paid/pending → in_progress + deadline + notif client
       Client : waiting_client → next phase / completed + notif artist
    =============================== */
    public function accept(Request $request)
    {
        $order  = Order::with(['service', 'client', 'artist'])
            ->where('order_id', $request->order_id)
            ->firstOrFail();
        $userId = Auth::user()->user_id;

        // ARTIST terima order baru
        if ($order->artist_id === $userId && in_array($order->status, ['pending', 'paid'])) {
            $order->update([
                'status' => 'in_progress',
                'phase'  => 'sketch',
            ]);
            $order->setDeadline();

            NotificationService::orderAccepted(
                $order->client_id,
                $order->order_id,
                $order->artist->name ?? 'Artist',
                $order->service->title ?? 'Commission'
            );

            return redirect()->route('artist.dashboard')
                ->with('success', 'Order diterima! Mulai kerjakan sketch.');
        }

        // CLIENT approve hasil
        if ($order->client_id === $userId && $order->status === 'waiting_client') {
            if ($order->phase === 'sketch') {
                $order->update([
                    'status' => 'in_progress',
                    'phase'  => 'coloring',
                ]);

                NotificationService::phaseApproved(
                    $order->artist_id,
                    $order->order_id,
                    $order->client->name ?? 'Client',
                    'coloring'
                );

                return redirect()->route('order.detail', $order->order_id)
                    ->with('success', 'Sketch disetujui! Artist akan mulai coloring.');
            } else {
                $order->update(['status' => 'completed']);

                NotificationService::orderCompleted(
                    $order->client_id,
                    $order->artist_id,
                    $order->order_id,
                    $order->service->title ?? 'Commission'
                );

                Log::info('Order completed', [
                    'order_id'  => $order->order_id,
                    'artist_id' => $order->artist_id,
                    'amount'    => $order->total_price,
                ]);

                return redirect()->route('order.detail', $order->order_id)
                    ->with('success', 'Order selesai! Terima kasih telah menggunakan Aneris.');
            }
        }

        abort(403);
    }

    /* ===============================
       REJECT — artist tolak order → notif client
    =============================== */
    public function reject(Request $request)
    {
        $order  = Order::with(['service', 'client'])
            ->where('order_id', $request->order_id)
            ->firstOrFail();
        $userId = Auth::user()->user_id;

        if ($order->artist_id !== $userId && $order->client_id !== $userId) abort(403);

        if (in_array($order->status, ['in_progress', 'waiting_client', 'completed'])) {
            return back()->with('error', 'Order tidak bisa dibatalkan di tahap ini.');
        }

        $order->update(['status' => 'canceled']);

        if ($order->artist_id === $userId) {
            NotificationService::orderRejected(
                $order->client_id,
                $order->order_id,
                Auth::user()->name,
                $order->service->title ?? 'Commission'
            );
        }

        return redirect()->route('artist.dashboard')
            ->with('success', 'Order ditolak.');
    }

    /* ===============================
       REVISION
       1. CLIENT request revisi: waiting_client → revision_requested
       2. ARTIST accept revisi:  revision_requested → revision
    =============================== */
    public function revision(Request $request)
    {
        $order  = Order::with(['artist', 'client'])
            ->where('order_id', $request->order_id)
            ->firstOrFail();
        $userId = Auth::user()->user_id;
        $action = $request->input('action'); // 'accept' | null

        // ARTIST menerima permintaan revisi
        if ($action === 'accept') {
            if ($order->artist_id !== $userId) abort(403);

            if ($order->status !== 'revision_requested') {
                return back()->with('error', 'Tidak ada permintaan revisi yang perlu diterima.');
            }

            $order->update(['status' => 'revision']);

            // Notif ke client bahwa artist mulai revisi
            NotificationService::send(
                $order->client_id,
                '🔧 Artist mulai mengerjakan revisi untuk ordermu.',
                'order',
                ['order_id' => $order->order_id]
            );

            return redirect()->route('artist.dashboard')
                ->with('success', 'Permintaan revisi diterima. Mulai kerjakan revisi!');
        }

        // CLIENT request revisi
        if ($order->client_id !== $userId) abort(403);

        if ($order->status !== 'waiting_client') {
            return back()->with('error', 'Tidak bisa request revisi saat ini.');
        }

        if ($order->revisionsLeft() <= 0) {
            return back()->with('error', 'Batas maksimal revisi sudah tercapai.');
        }

        $order->increment('revision_count');
        $order->update(['status' => 'revision_requested']);

        NotificationService::revisionRequested(
            $order->artist_id,
            $order->order_id,
            Auth::user()->name,
            $order->revision_count,
            $order->revision_limit ?? 2
        );

        return redirect()->route('order.detail', $order->order_id)
            ->with('success', 'Permintaan revisi dikirim ke artist.');
    }

    /* ===============================
       SEND RESULT — artist kirim hasil → notif client
    =============================== */
    public function sendResult(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,order_id',
            'result_file' => 'required|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:20480',
        ]);

        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->artist_id !== Auth::user()->user_id) abort(403);

        if (!in_array($order->status, ['in_progress', 'revision'])) {
            return back()->with('error', 'Tidak bisa kirim hasil di status ini.');
        }

        $currentPhase = $order->phase ?? 'sketch';

        $filePath = $request->file('result_file')
            ->store('orders/' . $order->order_id, 'public');

        $order->update([
            'status'     => 'waiting_client',
            'final_file' => $filePath,
        ]);

        NotificationService::resultSent(
            $order->client_id,
            $order->order_id,
            Auth::user()->name,
            $currentPhase
        );

        return redirect()->route('artist.dashboard')
            ->with('success', 'Hasil dikirim! Menunggu review client.');
    }

    /* ===============================
       COMPLETE — manual complete
    =============================== */
    public function complete(Request $request)
    {
        $order = Order::with(['service'])
            ->where('order_id', $request->order_id)
            ->firstOrFail();

        if ($order->artist_id !== Auth::user()->user_id) abort(403);

        $order->update(['status' => 'completed']);

        NotificationService::orderCompleted(
            $order->client_id,
            $order->artist_id,
            $order->order_id,
            $order->service->title ?? 'Commission'
        );

        return back()->with('success', 'Order diselesaikan.');
    }

    /* ===============================
       CLIENT CANCEL
    =============================== */
    public function clientCancel(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->client_id !== Auth::user()->user_id) abort(403);

        if (in_array($order->status, ['in_progress', 'waiting_client', 'completed'])) {
            return back()->with('error', 'Order tidak bisa dibatalkan di tahap ini.');
        }

        $order->update(['status' => 'canceled']);

        return redirect()->route('cart.index')->with('success', 'Order dibatalkan.');
    }

    /* ===============================
       REQUEST REFUND
    =============================== */
    public function requestRefund(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->client_id !== Auth::user()->user_id) abort(403);

        return back()->with('info', 'Permintaan refund telah dikirim. Tim kami akan menghubungimu.');
    }

    /* ===============================
       REQUEST EXTENSION (artist) → notif client
    =============================== */
    public function requestExtension(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,order_id',
            'reason'         => 'required|string|max:500',
            'extension_days' => 'required|integer|min:1|max:14',
        ]);

        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->artist_id !== Auth::user()->user_id) abort(403);

        if ($order->hasExtensionRequest()) {
            return back()->with('error', 'Permintaan perpanjangan waktu sudah pernah dikirim.');
        }

        $order->update([
            'extension_requested_at' => now(),
            'extension_reason'       => $request->reason,
            'extension_days'         => $request->extension_days,
            'extension_status'       => 'pending',
        ]);

        NotificationService::extensionRequested(
            $order->client_id,
            $order->order_id,
            Auth::user()->name,
            $request->extension_days,
            $request->reason
        );

        return back()->with('info', 'Permintaan perpanjangan waktu dikirim ke client.');
    }

    /* ===============================
       RESPOND EXTENSION (client) → notif artist
    =============================== */
    public function respondExtension(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'action'   => 'required|in:approve,reject',
        ]);

        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->client_id !== Auth::user()->user_id) abort(403);

        $approved = $request->action === 'approve';

        $order->update(['extension_status' => $approved ? 'approved' : 'rejected']);

        if ($approved && $order->deadline_at && $order->extension_days) {
            $order->update([
                'deadline_at' => $order->deadline_at->addDays($order->extension_days),
            ]);
        }

        NotificationService::extensionResponded(
            $order->artist_id,
            $order->order_id,
            Auth::user()->name,
            $approved ? 'approved' : 'rejected'
        );

        return back()->with(
            'info',
            $approved ? 'Perpanjangan waktu disetujui.' : 'Perpanjangan waktu ditolak.'
        );
    }
}
