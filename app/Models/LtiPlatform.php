<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LtiPlatform extends Model
{
    protected $fillable = [
        'tool_id',
        'issuer',
        'name',
        'client_id',
        'public_keyset_url',
        'access_token_url',
        'authentication_request_url',
        'authentication_service_provider',
        'authentication_service_url',
    ];

    public function tool()
    {
        return $this->belongsTo(LtiTool::class);
    }

    public function deployments()
    {
        return $this->hasMany(LtiPlatformDeployment::class, 'platform_id');
    }
}
