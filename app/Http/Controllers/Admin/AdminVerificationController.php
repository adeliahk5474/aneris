<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminVerificationController extends Controller
{
    // ── ANTREAN ──
    public function index(Request $request)
    {
        $currentStatus = $request->input('status', 'all');
        $currentSort   = $request->input('sort', 'latest');

        $query = PortfolioVerification::with('artist');

        if ($currentStatus !== 'all') {
            $query->where('status', $currentStatus);
        }

        match ($currentSort) {
            'oldest'     => $query->oldest(),
            'score_asc'  => $query->orderByRaw('ai_score_reference IS NULL, ai_score_reference ASC'),
            'score_desc' => $query->orderByRaw('ai_score_reference IS NULL, ai_score_reference DESC'),
            default      => $query->latest(),
        };

        $verifications = $query->paginate(20);
        $total         = PortfolioVerification::count();

        $counts = PortfolioVerification::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        return view('admin.verifications.index', compact(
            'verifications',
            'total',
            'counts',
            'currentStatus',
            'currentSort'
        ));
    }

    // ── DETAIL ──
    public function show(PortfolioVerification $verification)
    {
        $verification->load('artist');
        return view('admin.verifications.show', compact('verification'));
    }

    // ── TANDAI IN REVIEW ──
    public function take(PortfolioVerification $verification)
    {
        if ($verification->status !== 'pending') {
            return back()->with('error', 'Status bukan pending.');
        }

        $verification->update(['status' => 'in_review']);

        return back()->with('success', 'Submission ditandai sebagai In Review.');
    }

    // ── APPROVE / REJECT ──
    public function decide(Request $request, PortfolioVerification $verification)
    {
        if ($verification->status === 'approved') {
            return back()->with('error', 'Submission ini sudah approved.');
        }

        $request->validate([
            'action'                => 'required|in:approve,reject',
            'score_social_style'    => 'required|integer|min:0|max:10',
            'score_social_age'      => 'required|integer|min:0|max:10',
            'score_social_wip'      => 'required|integer|min:0|max:10',
            'score_social_comments' => 'required|integer|min:0|max:10',
            'score_portfolio'       => 'required|integer|min:0|max:60',
            'admin_notes_final'     => 'required|string|min:10',
        ]);

        $total = $request->score_social_style
            + $request->score_social_age
            + $request->score_social_wip
            + $request->score_social_comments
            + $request->score_portfolio;

        $isApprove = $request->action === 'approve';

        $verification->update([
            'status'                => $isApprove ? 'approved' : 'rejected',
            'score_social_style'    => $request->score_social_style,
            'score_social_age'      => $request->score_social_age,
            'score_social_wip'      => $request->score_social_wip,
            'score_social_comments' => $request->score_social_comments,
            'score_portfolio'       => $request->score_portfolio,
            'admin_notes_social'    => $request->admin_notes_social,
            'admin_notes_portfolio' => $request->admin_notes_portfolio,
            'admin_notes_final'     => $request->admin_notes_final,
            'total_score'           => $total,
            'admin_id'              => Auth::guard('admin')->id(),
            'reviewed_at'           => now(),
            'next_eligible_at'      => $isApprove ? null : now()->addDays(30),
        ]);

        // Update is_verified di users
        $artist = $verification->artist;
        if ($artist) {
            $artist->update(['is_verified' => $isApprove]);
        }

        $msg = $isApprove
            ? 'Artist berhasil diapprove dan sekarang berstatus Verified.'
            : 'Submission direject. Artist dikunci 30 hari.';

        return redirect()
            ->route('admin.verification.show', $verification->id)
            ->with('success', $msg);
    }
}
