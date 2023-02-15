<?php

namespace App\Http\Controllers;

use App\Models\LtiPlatform;
use App\Models\LtiPlatformDeployment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Platform extends Controller
{
    public function register(Request $request){
        DB::beginTransaction();
        try {
            //code...
            $platform = LtiPlatform::where('issuer', $request->issuer)->first();
            if($platform){
                $platform->name = $request->name;
                $platform->client_id = $request->client_id;
                $platform->public_keyset_url = $request->jwks_url;
                $platform->access_token_url = $request->access_token_url;
                $platform->authentication_request_url = $request->oidc_endpoint;
                $platform->save();
            }
            else {
                $platform = LtiPlatform::create([
                    'tool_id'   => 1,
                    'issuer'    => $request->issuer,
                    'name'      => $request->name,
                    'client_id' => $request->client_id,
                    'public_keyset_url' => $request->jwks_url,
                    'access_token_url'  => $request->access_token_url,
                    'authentication_request_url'    => $request->oidc_endpoint,
                ]);
            }
    
            $deployment = LtiPlatformDeployment::where('platform_id', $platform->id)->first();
            if($deployment){
                $deployment->deployment_id = $request->deployment_id;
                $deployment->save();
            }
            else {
                LtiPlatformDeployment::create([
                    'platform_id'   => $platform->id,
                    'deployment_id' => $request->deployment_id,
                ]);
            }
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }

    }
}
