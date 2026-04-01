<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommissionController extends Controller
{
	/**
	 * Create a commission order (client -> artist)
	 * Expected payload: artist_id, total_price, category_id, notes (optional), paid (bool)
	 */
	public function store(Request $request)
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

			// create initial chat thread for this order so artist & client can discuss
			$chat = Chat::create([
				'chat_id' => (string) Str::uuid(),
				'order_id' => $order->id ?? null,
				'sender_id' => Auth::id(),
				'receiver_id' => $data['artist_id'],
				'message' => 'Order created: ' . ($data['notes'] ?? 'No message'),
				'is_read' => false,
			]);

			return response()->json([
				'success' => true,
				'message' => 'Order created',
				'order' => $order,
				'chat_id' => $chat->chat_id,
			]);
		} catch (\Throwable $e) {
			Log::error('Failed to create commission order: ' . $e->getMessage());
			return response()->json(['success' => false, 'message' => 'Failed to create order'], 500);
		}
	}

}

