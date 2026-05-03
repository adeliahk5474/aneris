<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommissionService;
use Illuminate\Support\Facades\Auth;

class CommissionServiceController extends Controller
{
    /* ===============================
        SHOW (optional - untuk halaman detail nanti)
    ================================ */
    public function show($id)
    {
        $service = CommissionService::where('service_id', $id)->firstOrFail();

        return view('commission.detail', compact('service'));
    }

    /* ===============================
        UPDATE
    ================================ */
    public function update(Request $request, $id)
    {
        $service = CommissionService::findOrFail($id);

        // 🔒 Cek ownership (WAJIB)
        if ($service->artist_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // ✅ Validasi
        $request->validate([
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:1',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,category_id',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        // 📷 Update image (jika ada)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('commission_services', 'public');
            $service->image_url = asset('storage/' . $path);
        }

        // 📝 Update data
        $service->update([
            'title'       => $request->title,
            'price'       => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);

        return back()->with('success', 'Commission updated.');
    }

    /* ===============================
        DELETE
    ================================ */
    public function destroy($id)
    {
        $service = CommissionService::findOrFail($id);

        // 🔒 Cek ownership
        if ($service->artist_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $service->delete();

        return back()->with('success', 'Commission deleted.');
    }
}
