<?php

namespace App\Models\Traits;

use OAT\Library\Lti1p3Core\Tool\Tool;

trait LTITool
{
    private function makeTool($tool)
    {
        return new Tool(
            $tool->kid,                 // [required] identifier
            $tool->name,                // [required] name
            $tool->issuer,              // [required] audience
            $tool->authentication_url,  // [required] OIDC initiation url
            $tool->launch_url,          // [optional] default tool launch url
            $tool->deep_linking_url     // [optional] DeepLinking url
        );

        return $tool;
    }
}
