<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $allowedStatuses = [
            'pending',
            'processing',
            'resolved',
            'rejected',
        ];

        $query = Report::query()
            ->with([
                'user:id,name,email',
                'movie:id,title,slug',
                'episode:id,movie_id,season_id,episode_number,name',
            ])
            ->latest('created_at');

        if (in_array($filter, $allowedStatuses)) {
            $query->where('status', $filter);
        } else {
            $filter = 'pending';
        }

        $reports = $query
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'pending' => Report::where('status', 'pending')->count(),

            'processing' => Report::where('status', 'processing')->count(),

            'resolved' => Report::where('status', 'resolved')->count(),

            'rejected' => Report::where('status', 'rejected')->count(),
        ];

        return view(
            'admin.pages.reports.index',
            compact(
                'reports',
                'counts',
                'filter'
            )
        );
    }


    /**
     * Cập nhật trạng thái báo lỗi
     */
    public function updateStatus(
        Request $request,
        Report $report
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,processing,resolved,rejected',
            ],

            'admin_note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $report->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? $report->admin_note,
        ]);

        return back()->with(
            'success',
            'Đã cập nhật báo lỗi.'
        );
    }


    /**
     * Xóa báo lỗi
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return back()->with(
            'success',
            'Đã xóa báo lỗi.'
        );
    }
}