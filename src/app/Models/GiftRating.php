<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiftRating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gift_id',
        'user_id',
        'rating',
    ];
    protected $dateFormat = 'U';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function boot()
    {
        static::created(function ($model) {
            calculateGiftRating($model->gift_id);
        });

        static::updated(function ($model) {
            calculateGiftRating($model->gift_id);
        });

        static::deleting(function ($model) {
            calculateGiftRating($model->gift_id);
        });

        parent::boot();
    }

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

    /** Set Rating */
    public function setRatingAttribute($value)
    {
        $this->attributes['rating'] = round($value * 2) / 2;
    }
}
