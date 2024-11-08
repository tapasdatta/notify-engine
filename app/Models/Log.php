<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Log extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["user_id", "activity_type", "activity_details"];
}
