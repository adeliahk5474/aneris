<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartController extends Controller
{
	/**
	 * Add commission/order from cart (simple flow): create Order and initial Chat
	 * Expects: artist_id, total_price, category_id, notes, paid
	 */
	public function add(Request $request)
	{
		if (!Auth::check()) {
			return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
		}

		$data = $request->validate([
			'artist_id' => 'required|string',
			'total_price' => 'required|numeric',
			'category_id' => 'nullable|string',
			'notes' => 'nullable|string',
			'paid' => 'sometimes|boolean',
		]);

		try {
			$status = (!empty($data['paid']) && $data['paid']) ? 'Paid' : 'On Progress';

			$order = Order::create([
				'client_id' => Auth::id(),
				'artist_id' => $data['artist_id'],
				'category_id' => $data['category_id'] ?? null,
				'total_price' => $data['total_price'],
				'status' => $status,
			]);

			// create chat thread for this order
			$chat = Chat::create([
				'chat_id' => (string) Str::uuid(),
				'order_id' => $order->id ?? null,
				'sender_id' => Auth::id(),
				'receiver_id' => $data['artist_id'],
				'message' => 'Order placed via cart: ' . ($data['notes'] ?? 'No message'),
				'is_read' => false,
			]);

			// Return JSON with redirect target to chat thread for immediate discussion
			return response()->json([
				'success' => true,
				'message' => 'Order created',
				'order' => $order,
				'chat_id' => $chat->chat_id,
				'redirect' => route('chat.thread', $chat->chat_id),
			]);
		} catch (\Throwable $e) {
			Log::error('Cart add failed: ' . $e->getMessage());
			return response()->json(['success' => false, 'message' => 'Failed to add to cart'], 500);
		}
	}

}

