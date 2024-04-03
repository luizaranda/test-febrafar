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
            $query->whereBetween('start_date', [$startDate, $dueDate])
                ->orWhereBetween('due_date', [$startDate, $dueDate]);
        } elseif ($startDate != null) {
            $query->where('start_date', '>=', $startDate);
        } elseif ($dueDate != null) {
            $query->where('due_date', '<=', $dueDate);
        }
        return $query;
    }
}
