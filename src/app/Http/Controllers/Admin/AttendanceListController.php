<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceListController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))
            : Carbon::today();

        $attendances = Attendance::with(['user', 'attendanceRequests'])
            ->whereDate('date', $date->toDateString())
            ->get()
            ->map(function ($attendance) {

                $pendingRequest = $attendance->attendanceRequests
                    ->where('status', 'pending')
                    ->first();

                if ($pendingRequest) {

                    $data = $pendingRequest->request_data;

                    $clockIn  = isset($data['clock_in']) ? Carbon::parse($data['clock_in']) : null;
                    $clockOut = isset($data['clock_out']) ? Carbon::parse($data['clock_out']) : null;
                    $breaks   = collect($data['breaks'] ?? []);

                    $attendance->clock_in  = $clockIn;
                    $attendance->clock_out = $clockOut;

                    $totalBreakMinutes = $breaks->sum(function ($break) {
                        if (!empty($break['start']) && !empty($break['end'])) {
                            return Carbon::parse($break['end'])
                                ->diffInMinutes(Carbon::parse($break['start']));
                        }
                        return 0;
                    });

                    $attendance->total_break_time = gmdate('H:i', $totalBreakMinutes * 60);

                    if ($clockIn && $clockOut) {
                        $workMinutes = $clockOut->diffInMinutes($clockIn) - $totalBreakMinutes;
                        $attendance->working_time = gmdate('H:i', $workMinutes * 60);
                    } else {
                        $attendance->working_time = null;
                    }
                }

                return $attendance;
            });

        return view('admin.attendances.index', compact('attendances', 'date'));
    }
}