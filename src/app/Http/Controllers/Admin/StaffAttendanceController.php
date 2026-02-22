<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffAttendanceController extends Controller
{
    public function index(Request $request, User $user)
    {
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month)->startOfMonth();
        } else {
            $month = now()->startOfMonth();
        }

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth(),
            ])
            ->get()
            ->keyBy(fn ($a) => $a->date->format('Y-m-d'));

        $days = [];

        $start = $month->copy();
        $end   = $month->copy()->endOfMonth();

        while ($start->lte($end)) {

            $key = $start->format('Y-m-d');
            $attendance = $attendances[$key] ?? null;

            if ($attendance) {

                $pendingRequest = AttendanceRequest::where('attendance_id', $attendance->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->first();

                if ($pendingRequest) {
                    $data = $pendingRequest->request_data;

                    $attendance->clock_in  = $data['clock_in']  ?? $attendance->clock_in;
                    $attendance->clock_out = $data['clock_out'] ?? $attendance->clock_out;
                }
            }

            $days[] = [
                'date' => $start->copy(),
                'attendance' => $attendance,
            ];

            $start->addDay();
        }

        return view('admin.attendances.staff', compact(
            'user',
            'days',
            'month'
        ));
    }

    public function exportCsv(Request $request, User $user)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->month)->startOfMonth()
            : now()->startOfMonth();

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        $fileName = $user->name . '_' . $month->format('Y_m') . '_attendance.csv';

        $response = new StreamedResponse(function () use ($attendances) {

            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                '日付',
                '出勤',
                '退勤',
                '休憩',
                '合計'
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->date->format('Y-m-d'),
                    optional($attendance->clock_in)?->format('H:i'),
                    optional($attendance->clock_out)?->format('H:i'),
                    $attendance->total_break_time ?? '',
                    $attendance->working_time ?? '',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="'.$fileName.'"'
        );

        return $response;
    }
}
