<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'secure_id'];

    protected static function booted()
    {
        static::creating(function ($task) {
            if (empty($task->secure_id)) {
                $task->secure_id = (string) Str::uuid();
            }
        });
    }

    protected $dates = ['deleted_at'];
}
