<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeWithTasksOrdered($query)
    {
        return $query->with('tasks')->orderBy('order');
    }

    public function completeRate(): int
    {
        $totalTasks = $this->tasks->count();
        $completedTasks = $this->tasks->where('status', '完了')->count();

        //完了したタスクが100%中何%か送る
        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    }
}
