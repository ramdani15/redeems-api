<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'gift_id',
        'user_id',
    ];
    protected $dateFormat = 'U';
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /** Get Gift */
    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

    /** Get User */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
