<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'status',
        'request_data',
        'reason',
        'requested_at',
        'approved_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}
