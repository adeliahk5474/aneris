<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CommissionService;
use App\Models\CommissionRequest;

class CommissionController extends Controller
{
    // show() tetap sama...

    // request: buat CommissionRequest lalu return JSON
    public function request(Request $request, $service_id)
    {
        if (!Auth::check()) {
            return response()->json(['success'=>false, 'needLogin'=>true, 'message'=>'Login required'], 401);
        }

        $request->validate([
            'description' => 'required|string|max:1000',
            'proposed_price' => 'nullable|numeric|min:0'
        ]);

        $service = CommissionService::findOrFail($service_id);

        $commissionRequest = CommissionRequest::create([
            'client_id' => Auth::id(),
            'artist_id' => $service->artist_id,
            'category_id' => $service->category_id,
            'description' => $request->description,
            'proposed_price' => $request->proposed_price ?? $service->price,
            'status' => 'pending_payment', // pending_payment agar jelas tahap
        ]);

        return response()->json([
            'success' => true,
            'commission_request_id' => $commissionRequest->id,
            'amount' => (float)$commissionRequest->proposed_price,
            'message' => 'Request created'
        ]);
    }
}
