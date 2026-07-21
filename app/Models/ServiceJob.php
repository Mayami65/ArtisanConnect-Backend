<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['client_id', 'title', 'description', 'category', 'budget', 'location', 'status', 'images', 'latitude', 'longitude'])]
class ServiceJob extends Model
{
    use SoftDeletes;
    protected $appends = ['distance_in_km'];
    protected $casts = [
        'images' => 'array',
    ];

    public function getDistanceInKmAttribute()
    {
        return $this->attributes['distance_in_km'] ?? null;
    }
    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function applications() {
        return $this->hasMany(Application::class);
    }
    public function reviews() {
        return $this->hasMany(Review::class);
    }
}
