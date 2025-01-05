<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportReply extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'replied_by')->withTrashed();
    }
}
