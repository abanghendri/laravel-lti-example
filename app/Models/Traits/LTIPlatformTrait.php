<?php

namespace App\Models\Traits;

use OAT\Library\Lti1p3Core\Platform\Platform;

trait LTIPlatformTrait
{
    private function makePlatform($platform)
    {
        $platform = new Platform(
            $platform->client_id,                        // [required] identifier
            $platform->name,                               // [required] name
            $platform->issuer,                      // [required] audience
            $platform->authentication_request_url,  // [optional] OIDC authentication url
            $platform->access_token_url,            // [optional] OAuth2 access token url
        );

        return $platform;
    }
}
