<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(ThreatReport::class, 'report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
