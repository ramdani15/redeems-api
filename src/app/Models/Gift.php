<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'stock',
        'total_purchases',
        'point',
        'rating',
        'image',
    ];
    protected $dateFormat = 'U';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** Relation to Ratings */
    public function ratings()
    {
        return $this->hasMany(GiftRating::class);
    }

    /** Relation to Likes */
    public function likes()
    {
        return $this->hasMany(GiftLike::class);
    }

    /** Get the Orders. */
    public function orders()
    {
        return $this->morphMany(Order::class, 'attachable');
    }

    /** Get Total Like */
    public function getTotalLikeAttribute()
    {
        return $this->likes()->count();
    }

    /** Get Image Url */
    public function getImageUrlAttribute()
    {
        if (file_exists(public_path().'/storage//'. $this->image)) {
            $url = '/storage/'.$this->image;
        } else {
            if ($this->image) {
                return $this->image;
            }
            $url = 'images/default.png';
        }
        return config('app.url').$url;
    }
}
