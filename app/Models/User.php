<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'category', 'hourly_rate', 'bio', 'latitude', 'longitude'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jobs() {
        return $this->hasMany(ServiceJob::class, 'client_id');
    }

    public function applications() {
        return $this->hasMany(Application::class, 'artisan_id');
    }

    public function reviewsReceived() {
        return $this->hasMany(Review::class, 'artisan_id');
    }

    public function reviewsGiven() {
        return $this->hasMany(Review::class, 'client_id');
    }
}
