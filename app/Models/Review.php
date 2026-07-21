<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['service_job_id', 'client_id', 'artisan_id', 'rating', 'comment'])]
class Review extends Model
{
    public function serviceJob() {
        return $this->belongsTo(ServiceJob::class);
    }
    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function artisan() {
        return $this->belongsTo(User::class, 'artisan_id');
    }
}
