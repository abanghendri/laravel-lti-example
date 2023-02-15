<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LtiTool extends Model
{
    protected $fillable = [
        'kid',
        'client_id',
        'name',
        'issuer',
        'description',
        'public_keyset_url',
        'launch_url',
        'access_token_url',
        'deep_linking_url',
        'content-selection_url',
        'type',
        'icon_url',
        'secure_icon_url',
        'ags_service',
        'nrp_service',
        'tool_setting_service',
        'deep_linking_service',
        'custom_properties'
    ];
}
