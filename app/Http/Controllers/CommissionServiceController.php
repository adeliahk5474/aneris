<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommissionService;
use Illuminate\Support\Facades\Auth;

class CommissionServiceController extends Controller
{
    // ---------------------------
    // EDIT
    // ---------------------------
    public function edit($id)
    {
        $service = CommissionService::where('service_id', $id)->firstOrFail();

        if ($service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('commission.edit', compact('service'));
    }

    // ---------------------------
    // UPDATE
    // ---------------------------
    public function update(Request $request, $id)
    {
        $service = CommissionService::where('service_id', $id)->firstOrFail();

        if ($service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'description' => 'required|string',
            'category_id' => 'uuid|nullable',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('commission', 'public');
            $service->image_url = asset('storage/' . $path);
        }

        $service->title = $request->title;
        $service->price = $request->price;
        $service->description = $request->description;
        $service->category_id = $request->category_id;
        $service->save();

        return redirect()->route('home')->with('success', 'Commission updated.');
    }

    // ---------------------------
    // DELETE
    // ---------------------------
    public function destroy($id)
    {
        $service = CommissionService::where('service_id', $id)->firstOrFail();

        if ($service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $service->delete();

        return redirect()->route('home')->with('success', 'Commission deleted.');
    }
}
