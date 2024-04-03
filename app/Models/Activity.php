<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'description',
        'start_date',
        'due_date',
        'completion_date',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilterByDateRange($query, $startDate, $dueDate)
    {
        if ($startDate != null && $dueDate != null) {
            $query->whereBetween('start_date', [$startDate . " 00:00:00", $dueDate . " 23:59:59"])
                ->orWhereBetween('due_date', [$startDate . " 00:00:00", $dueDate . " 23:59:59"]);
        } elseif ($startDate != null) {
            $query->where('start_date', '>=', $startDate . " 00:00:00");
        } elseif ($dueDate != null) {
            $query->where('due_date', '<=', $dueDate . " 23:59:59");
        }
        return $query;
    }
}
