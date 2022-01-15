<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     */
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'is_approve',
    ];
    /**
     * Get user associated with model.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->select('id','name');
    }
}
