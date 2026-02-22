<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use App\Http\Requests\AttendanceUpdateRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth('web')->user();
        $today = today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return view('attendance.index', compact('attendance'));
    }

    public function clockIn()
    {
        Attendance::firstOrCreate(
            [
                'user_id' => auth('web')->id(),
                'date' => today(),
            ],
            [
                'clock_in' => now(),
            ]
        );

        return back();
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', auth('web')->id())
            ->where('date', today())
            ->firstOrFail();

        if ($attendance->clock_out) {
            return back();
        }

        $attendance->update([
            'clock_out' => now(),
        ]);

        return back()->with('clocked_out', true);
    }

    public function breakIn()
    {
        $attendance = Attendance::where('user_id', auth('web')->id())
            ->where('date', today())
            ->firstOrFail();

        $onBreak = $attendance->breakTimes()
            ->whereNull('end_time')
            ->exists();

        if ($onBreak) {
            return back();
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => now(),
        ]);

        return back();
    }

    public function breakOut()
    {
        $attendance = Attendance::where('user_id', auth('web')->id())
            ->where('date', today())
            ->firstOrFail();

        $break = $attendance->breakTimes()
            ->whereNull('end_time')
            ->latest()
            ->first();

        if (! $break) {
            return back();
        }

        $break->update([
            'end_time' => now(),
        ]);

        return back();
    }

    public function detail($date)
    {
        $user = auth('web')->user();

        $attendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'date' => $date,
        ]);

        $attendance->load([
            'breakTimes' => fn ($q) => $q->orderBy('start_time'),
            'user',
            'attendanceRequests'
        ]);

        $pendingRequest = $attendance->attendanceRequests
            ->where('status', 'pending')
            ->first();

        $hasPendingRequest = $pendingRequest !== null;

        return view('attendance.detail', compact(
            'attendance',
            'hasPendingRequest',
            'pendingRequest'
        ));
    }

    public function adminDetail($userId, $date)
    {
        $attendance = Attendance::firstOrCreate([
            'user_id' => $userId,
            'date' => $date,
        ]);

        $attendance->load([
            'breakTimes' => fn ($q) => $q->orderBy('start_time'),
            'user',
            'attendanceRequests'
        ]);

        $pendingRequest = $attendance->attendanceRequests
            ->where('status', 'pending')
            ->first();

        $hasPendingRequest = $pendingRequest !== null;

        return view('attendance.detail', compact(
            'attendance',
            'hasPendingRequest',
            'pendingRequest'
        ));
    }

    public function list(Request $request)
    {
        $user = auth('web')->user();

        $month = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))
            : now();

        $attendances = Attendance::with(['breakTimes', 'attendanceRequests'])
            ->where('user_id', $user->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
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
            })
            ->keyBy(fn ($item) => $item->date->format('Y-m-d'));

        $days = [];
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        while ($start->lte($end)) {

            $dateKey = $start->format('Y-m-d');

            $days[] = [
                'date' => $start->copy(),
                'attendance' => $attendances[$dateKey] ?? null
            ];

            $start->addDay();
        }

        return view('attendance.list', compact('days', 'month'));
    }

    public function update(AttendanceUpdateRequest $request, Attendance $attendance)
    {
        if (auth('admin')->check()) {

            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'note'      => $request->note,
            ]);

            $attendance->breakTimes()->delete();

            if ($request->breaks) {
                foreach ($request->breaks as $break) {

                    if (empty($break['start']) || empty($break['end'])) {
                        continue;
                    }

                    $attendance->breakTimes()->create([
                        'start_time' => $break['start'],
                        'end_time'   => $break['end'],
                    ]);
                }
            }

            return redirect()->route('admin.attendance.detail', [
                'user' => $attendance->user_id,
                'date' => $attendance->date->format('Y-m-d'),
            ])->with('message', '勤怠を更新しました');
        }

        AttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'status'        => 'pending',
            'reason'        => '勤怠修正',
            'requested_at'  => now(),
            'request_data'  => [
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'breaks'    => $request->breaks,
                'note'      => $request->note,
            ],
        ]);

        return redirect()
            ->route('attendance.list')
            ->with('message', '修正申請を送信しました');
    }
}