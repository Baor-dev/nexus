<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        // THÊM App\Models\Comment VÀO DANH SÁCH VALIDATION
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|in:App\Models\Post,App\Models\User,App\Models\Comment', // <--- Thêm Comment
            'reason' => 'required|string|max:255',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'reportable_id' => $request->reportable_id,
            'reportable_type' => $request->reportable_type,
            'reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xem xét.']);
    }
}