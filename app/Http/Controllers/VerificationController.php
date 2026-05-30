<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\PortfolioVerification;
use App\Services\PortfolioAnalyzer;

class VerificationController extends Controller
{
    public function create()
    {
        $user   = Auth::user();
        $latest = PortfolioVerification::where('artist_id', $user->user_id)
            ->latest()
            ->first();

        // Sudah approved — redirect ke status
        if ($user->is_verified) {
            return redirect()->route('verification.status');
        }

        // Ada submission aktif
        if ($latest && in_array($latest->status, ['pending', 'in_review'])) {
            return redirect()->route('verification.status');
        }

        // Cooldown
        if (
            $latest && $latest->status === 'rejected'
            && $latest->next_eligible_at
            && now()->lt($latest->next_eligible_at)
        ) {
            return redirect()->route('verification.status');
        }

        return view('pages.verification.create', compact('latest'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // ── Guard 1: sudah verified ──
        if ($user->is_verified) {
            return back()->with('error', 'Kamu sudah terverifikasi.');
        }

        // ── Guard 2: submission aktif ──
        $existing = PortfolioVerification::where('artist_id', $user->user_id)
            ->whereIn('status', ['pending', 'in_review'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Submisimu sedang dalam antrian review. Harap tunggu.');
        }

        // ── Guard 3: cooldown ──
        $lastRejected = PortfolioVerification::where('artist_id', $user->user_id)
            ->where('status', 'rejected')
            ->latest('reviewed_at')
            ->first();

        if ($lastRejected && $lastRejected->next_eligible_at && now()->lt($lastRejected->next_eligible_at)) {
            $daysLeft = (int) now()->diffInDays($lastRejected->next_eligible_at, false);
            return back()->with('error', "Kamu bisa submit ulang dalam {$daysLeft} hari lagi.");
        }

        // ── Validasi ──
        $request->validate([
            'portfolio_files'      => 'required|array|min:3|max:10',
            'portfolio_files.*'    => 'required|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic,tiff,avif|max:20480',
            'social_media_links'   => 'required|array|min:1',
            'social_media_links.*' => 'nullable|url|max:500',
            'declaration'          => 'required|accepted',
        ], [
            'portfolio_files.required'    => 'Upload minimal 3 file gambar portofolio.',
            'portfolio_files.min'         => 'Upload minimal 3 file portofolio.',
            'portfolio_files.max'         => 'Maksimal 10 file portofolio.',
            'portfolio_files.*.mimes'     => 'Format file harus berupa gambar (JPG, PNG, WEBP, GIF, dll).',
            'portfolio_files.*.max'       => 'Ukuran tiap file maksimal 20MB.',
            'social_media_links.required' => 'Tambahkan minimal 1 link sosial media.',
            'social_media_links.*.url'    => 'Format link tidak valid. Gunakan URL lengkap (https://...).',
            'declaration.accepted'        => 'Kamu harus menyetujui pernyataan keaslian karya.',
        ]);

        // ── Simpan file ──
        $filePaths = [];
        foreach ($request->file('portfolio_files') as $file) {
            $url = \App\Services\CloudinaryService::upload(
                $file,
                'verifications/' . $user->user_id
            );
            $filePaths[] = [
                'path' => $url,  // sekarang path = Cloudinary URL
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'ext'  => strtolower($file->getClientOriginalExtension()),
            ];
        }

        // ── Filter link sosmed kosong ──
        $socialLinks = array_values(array_filter(
            $request->input('social_media_links', []),
            fn($l) => !empty(trim($l ?? ''))
        ));

        // ── AI Pre-screening ──
        $analyzer = new PortfolioAnalyzer();
        $result   = $analyzer->analyze($request->file('portfolio_files'));

        // ── Buat record ──
        PortfolioVerification::create([
            'artist_id'          => $user->user_id,
            'status'             => 'pending',
            'portfolio_files'    => $filePaths,
            'social_media_links' => $socialLinks,
            'ai_score_reference' => $result['score'],
            'ai_score_notes'     => $result['notes'],
            'ai_breakdown'       => $result['breakdown'],
        ]);

        return redirect()
            ->route('verification.status')
            ->with('success', 'Submisi berhasil dikirim! Tim kami akan mereview dalam 3–5 hari kerja.');
    }

    public function status()
    {
        $user   = Auth::user();
        $latest = PortfolioVerification::where('artist_id', $user->user_id)
            ->latest()
            ->first();

        if (!$latest && !$user->is_verified) {
            return redirect()->route('verification.create');
        }

        return view('pages.verification.status', compact('latest'));
    }
}
