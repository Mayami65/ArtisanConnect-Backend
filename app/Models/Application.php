<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['service_job_id', 'artisan_id', 'status', 'proposal'])]
class Application extends Model
{
    public function serviceJob() {
        return $this->belongsTo(ServiceJob::class);
    }
    public function artisan() {
        return $this->belongsTo(User::class, 'artisan_id');
    }
}
