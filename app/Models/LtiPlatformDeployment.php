<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LtiPlatformDeployment extends Model
{
    protected $fillable = [
        'platform_id',
        'deployment_id',
    ];
}
