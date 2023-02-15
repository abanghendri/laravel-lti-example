<?php

namespace App\Http\Controllers;

use App\Models\LtiPlatform;
use App\Models\LtiPlatformDeployment;
use App\Models\LtiTool;
use App\Models\Traits\LTIAgService;
use App\Models\Traits\LTISecurity;
use Illuminate\Http\Request;
use OAT\Library\Lti1p3Ags\Service\LineItem\Client\LineItemServiceClient;
use OAT\Library\Lti1p3Ags\Service\LineItem\LineItemServiceInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Result\LaunchValidationResultInterface;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLink;
use OAT\Library\Lti1p3Core\Resource\ResourceCollection;
use OAT\Library\Lti1p3Core\Security\Jwt\Token;
use OAT\Library\Lti1p3DeepLinking\Message\Launch\Builder\DeepLinkingLaunchResponseBuilder;
use Psr\Http\Message\ServerRequestInterface;

class LaunchController extends Controller
{
    use LTISecurity;
    use LTIAgService;
    
    public function launch(ServerRequestInterface $request)
    {
        $result = $this->validateRequest($request);
        if (!$result->hasError()) {
            $courseClaim = $result->getPayload()->getCustom()['course'];
            // from Request Validation
            $registration = $result->getRegistration();

            return view('lti-content', [
                'course' => $courseClaim,
                'user' => $result->getPayload()->getUserIdentity()
            ]);
        }
        else {
            dd($result->getError());
        }
    }

    private function agsService(LaunchValidationResultInterface $result)
    {
        $agsClaims = $result->getPayload()->getAgs();
        $agsClaimScopes = $agsClaims->getScopes();
        

        // Related registration
        /** @var RegistrationRepositoryInterface $registrationRepository */
        $registration = $result->getRegistration();

        $lineItemClient = new LineItemServiceClient();
        $lineItem = $lineItemClient->getLineItem(
            // [required] as the tool, it will call the platform of this registration
            $registration,
            // [required] AGS line item url
            $agsClaims->getLineItemUrl(),
            // [optional] scopes to use (default both read only and regular line item scopes)
            [LineItemServiceInterface::AUTHORIZATION_SCOPE_LINE_ITEM]
        );

        return $lineItem;
        // Line item identifier
        // echo $lineItem->getIdentifier();

        // // Line item max score
        // echo $lineItem->getScoreMaximum();
    }

    public function deepLink(ServerRequestInterface $request)
    {
        $result = $this->validateRequest($request);
        if (!$result->hasError()) {
            session()->forget('deep-linking-request');
            session()->put('deep-linking-request', $result);
            session()->save();
            return view('select-content');
        }
    }

    public function selectedContent(ServerRequestInterface $request)
    {
        $result = session()->get('deep-linking-request');
        $payload = (object) getPayload($request);
        if ($result) {
                
            $ltiResourceLink = new LtiResourceLink('ltiResourceLinkIdentifier', [
                'url' => route('launch'),
                'title' => 'Course content '.$payload->content,
                'custom'    => [
                    'course' => $payload->content
                ],
            ]);
                
            // Aggregate them in a collection
            $resourceCollection = new ResourceCollection();
            $resourceCollection
                ->add($ltiResourceLink);
    
            //Build Deep Link response
    
            /** @var LaunchValidationResultInterface $result */
    
            // Create a builder instance
            $builder = new DeepLinkingLaunchResponseBuilder();
    
            // Related deep linking platform settings claim from previous steps
            $deepLinkingSettingsClaim = $result->getPayload()->getDeepLinkingSettings();
            // Get related registration of the launch

            /** @var RegistrationRepositoryInterface $registrationRepository */
            $registration = $result->getRegistration();
    
            // Build a deep linking response launch message
            $message = $builder->buildDeepLinkingLaunchResponse(
                $resourceCollection,
                $registration,
                $deepLinkingSettingsClaim->getDeepLinkingReturnUrl(),
                null,
                $deepLinkingSettingsClaim->getData(),
                '1 course selected',
            );
            
            // return HTML form containing the auto generated JWT parameter
            return $message->toHtmlRedirectForm();
        }
        return "Session expired";
    }
}
