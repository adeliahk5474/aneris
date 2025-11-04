<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CommissionRequest;

class CartController extends Controller
{
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success'=>false, 'message'=>'Login required'], 401);
        }

        $request->validate([
            'commission_request_id' => 'required|exists:commission_requests,id'
        ]);

        $cr = CommissionRequest::findOrFail($request->commission_request_id);

        // find or create cart for user
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['status' => 'open'] // sesuaikan skema cart
        );

        // create cart item
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'commission_request_id' => $cr->id,
            'price' => $cr->proposed_price,
            'quantity' => 1
        ]);

        // optionally update commission_request status to 'in_cart' or keep 'pending_payment'
        $cr->update(['status' => 'in_cart']);

        return response()->json([
            'success' => true,
            'cart_id' => $cart->id,
            'cart_item_id' => $item->id,
            'message' => 'Added to cart'
        ]);
    }
}
