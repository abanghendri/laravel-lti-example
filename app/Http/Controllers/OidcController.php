<?php

namespace App\Http\Controllers;

use App\Models\Traits\LTISecurity;
use Illuminate\Http\Request;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\Jwk\JwkRS256Exporter;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcInitiator;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;

use Psr\Http\Message\ServerRequestInterface;

class OidcController extends Controller
{
    use LTISecurity;

    /**
    * expose Jason Web Key Set.
    *
    * @return OAT\Library\Lti1p3Core\Security\Jwks\Exporter\Jwk\JwksExportInterface
    */
    public function jwks()
    {
        return [ 'keys' => [(new JwkRS256Exporter())->export($this->toolKeyChain())]];
    }

    public function init(ServerRequestInterface $request)
    {
        $payload = getPayload($request);
        if(!$payload){
            return abort(403);
        }
        // session()->forget('lti-registration');
        $registration = $this->registration($request);
        // session()->put('registration', $registration);
        // session()->save();
        $registrationRepository = $this->createRegistrationRepository([$registration]);
        /** @var RegistrationRepositoryInterface $registrationRepository */

        // Create the OIDC initiator
        $initiator = new OidcInitiator($registrationRepository);
        
        // Perform the OIDC initiation (including state generation)
        $message = $initiator->initiate($request);
        
        return redirect($message->toUrl());
    }
}
