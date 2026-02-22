<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->breakTimes->sum(function ($break) {
            if (!$break->start_time || !$break->end_time) {
                return 0;
            }
            return Carbon::parse($break->start_time)
                ->diffInMinutes(Carbon::parse($break->end_time));
        });
    }

    public function getTotalBreakTimeAttribute()
    {
        $minutes = $this->total_break_minutes;
        return sprintf('%d:%02d', floor($minutes / 60), $minutes % 60);
    }

    public function getWorkingMinutesAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }

        $total = Carbon::parse($this->clock_in)
            ->diffInMinutes(Carbon::parse($this->clock_out));

        return $total - $this->total_break_minutes;
    }

    public function getWorkingTimeAttribute()
    {
        if (is_null($this->working_minutes)) {
            return null;
        }

        return sprintf(
            '%d:%02d',
            floor($this->working_minutes / 60),
            $this->working_minutes % 60
        );
    }

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }
    
}
