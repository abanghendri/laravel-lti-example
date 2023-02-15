<?php

namespace Database\Seeders;

use App\Models\LtiTool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class LtiToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LtiTool::create([
            'kid'                       => Str::random(20),
            'client_id'                 => Str::random(15),
            'name'                      => config('app.name'),
            'issuer'                    => config('app.url'),
            'public_keyset_url'         => config('app.url').'/.well-known/jwks.json',
            // 'access_token_url'          => ,
            'launch_url'                => config('app.url').'/launch',
            'authentication_url'        => config('app.url').'/auth',
            'deep_linking_url'          => config('app.url').'/launch/deeplink',
             
        ]);
    }
}
