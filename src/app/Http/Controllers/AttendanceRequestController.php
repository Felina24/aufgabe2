<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\BreakTime;
use App\Http\Requests\AttendanceUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendanceRequestController extends Controller
{
    public function store(AttendanceUpdateRequest $request, Attendance $attendance)
    {
        $exists = AttendanceRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'すでに申請中です');
        }

        AttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'status' => 'pending',
            'request_data' => [
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_out,
                'breaks' => $request->breaks,
                'note' => $request->note,
            ],
            'reason' => '勤怠修正',
            'requested_at' => now(),
        ]);

        return redirect()
            ->route('request.list')
            ->with('message', '修正申請を送信しました');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = AttendanceRequest::with(['attendance.user']);

        if (!Auth::guard('admin')->check()) {

            $query->whereHas('attendance', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $query->where('status', $status === 'approved' ? 'approved' : 'pending');

        $requests = $query
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('request.list', compact('requests', 'status'));
    }

    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::with([
            'attendance.user',
            'attendance.breakTimes'
        ])->findOrFail($id);

        if (Auth::guard('admin')->check()) {
            return view('request.detail', [
                'request' => $attendanceRequest
            ]);
        }

        if (Auth::guard('web')->check()) {

            if ($attendanceRequest->attendance->user_id !== Auth::id()) {
                abort(403);
            }

            return view('request.detail', [
                'request' => $attendanceRequest
            ]);
        }

        abort(403);
    }

    public function approve(AttendanceRequest $attendanceRequest)
    {
        DB::transaction(function () use ($attendanceRequest) {

            $attendance = $attendanceRequest->attendance;
            $data = $attendanceRequest->request_data;

            $attendance->update([
                'clock_in' => $data['clock_in'] ?? null,
                'clock_out' => $data['clock_out'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            $attendance->breakTimes()->delete();

            if (!empty($data['breaks'])) {
                foreach ($data['breaks'] as $break) {

                    if (empty($break['start']) || empty($break['end'])) {
                        continue;
                    }

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => $break['start'],
                        'end_time' => $break['end'],
                    ]);
                }
            }

            $attendanceRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.request.list')
            ->with('message', '申請を承認しました');
    }

    public function showUser($attendanceRequest)
    {
        $request = \App\Models\AttendanceRequest::with(['attendance.user'])
            ->findOrFail($attendanceRequest);

        if ($request->attendance->user_id !== auth()->id()) {
            abort(403);
        }

        return view('request.detail', compact('request'));
    }

}
