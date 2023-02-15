<?php

namespace App\Models\Traits;

use App\Models\LtiPlatform;
use App\Models\LtiPlatformDeployment;
use App\Models\LtiTool;
use App\Models\Traits\LTITool as TraitsLTITool;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Registration\Registration;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Jwt\Parser\Parser;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactory;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\Nonce;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

trait LTISecurity
{
    use TraitsLTITool;
    use LTIPlatformTrait;

    /**
     * make keychain for tool
     */
    private function toolKeyChain($tool = null)
    {
        if ($tool === null) {
            $tool = LtiTool::first();
        }
        $keyChain = (new KeyChainFactory)->create(
            $tool->kid,                         // [required] identifier (used for JWT kid header)
            $tool->name,                        // [required] key set name (for grouping)
            file_get_contents(storage_path('oauth-public.key')), // [required] public key (file or content)
            file_get_contents(storage_path('oauth-private.key')),     // [optional] private key (file or content)
            '',                             // [optional] private key passphrase (if existing)
            KeyInterface::ALG_RS256            // [optional] algorithm (default: RS256)
        );
        return $keyChain;
    }

    private function registration(ServerRequestInterface $request)
    {
        $params = (object) getPayload($request);
        $registration = LtiPlatform::where('issuer', $params->iss)->first();
        
        if (!$registration) {
            return "Your platform is not registered yet";
        }

        $deployment =LtiPlatformDeployment::where('platform_id', $registration->id)
                                    ->where('deployment_id', $params->lti_deployment_id)
                                    ->first();
        if (!$deployment) {
            $deployment = LtiPlatformDeployment::create([
                'platform_id'       => $registration->id,
                'deployment_id'     => $params->lti_deployment_id,
            ]);
        }

        $tool = $this->makeTool($registration->tool);
        $platform = $this->makePlatform($registration);
        $registration = new Registration(
            $params->login_hint,
            $params->client_id,
            $platform,
            $tool,
            [$deployment->deployment_id],
            null,
            $this->toolKeyChain($registration->tool),
            $registration->public_keyset_url,
            $registration->tool->public_keyset_url,
        );
        return $registration;
    }

    private function createRegistrationRepository(array $registrations = []): RegistrationRepositoryInterface
    {
        // $registrations = !empty($registrations)
        //     ? $registrations
        //     : [$this->registration()];

        return new class ($registrations) implements RegistrationRepositoryInterface
        {
            /** @var RegistrationInterface[] */
            private $registrations;

            /** @param RegistrationInterface[] $registrations */
            public function __construct(array $registrations)
            {
                foreach ($registrations as $registration) {
                    $this->registrations[$registration->getIdentifier()] = $registration;
                }
            }

            public function find(string $identifier): ?RegistrationInterface
            {
                return $this->registrations[$identifier] ?? null;
            }

            public function findAll(): array
            {
                return $this->registrations;
            }

            public function findByClientId(string $clientId): ?RegistrationInterface
            {
                foreach ($this->registrations as $registration) {
                    if ($registration->getClientId() === $clientId) {
                        return $registration;
                    }
                }

                return null;
            }

            public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
            {
                foreach ($this->registrations as $registration) {
                    if ($registration->getPlatform()->getAudience() === $issuer) {
                        if (null !== $clientId) {
                            if ($registration->getClientId() === $clientId) {
                                return $registration;
                            }
                        } else {
                            return $registration;
                        }
                    }
                }

                return null;
            }

            public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
            {
                foreach ($this->registrations as $registration) {
                    if ($registration->getTool()->getAudience() === $issuer) {
                        if (null !== $clientId) {
                            if ($registration->getClientId() === $clientId) {
                                return $registration;
                            }
                        } else {
                            return $registration;
                        }
                    }
                }

                return null;
            }
        };
    }

    private function validateRequest(ServerRequestInterface $request)
    {
        
        $registrationRepository = $this->getRegistrationRepository($request);
        /** @var NonceRepositoryInterface $nonceRepository */
        $nonceRepository = $this->createNonceRepository();

        // Create the validator
        $validator = new ToolLaunchValidator($registrationRepository, $nonceRepository);
        // Perform validation
        $result = $validator->validatePlatformOriginatingLaunch($request);
        return $result;
    }

    private function getRegistrationRepository(ServerRequestInterface $request)
    {
        $payload = (object) getPayload($request);
        $parser = new Parser();
        $token = $parser->parse($payload->id_token);
        $claims = $token->getClaims();
        /** @var RegistrationRepositoryInterface $registrationRepository */
        $registrationRepository = $this->getRegistrationRepositoryFromClaims($claims);
        return $registrationRepository;
    }

    private function createNonceRepository(
        array $nonces = [],
        bool $withAutomaticFind = false
    ): NonceRepositoryInterface {
        $nonces = !empty($nonces) ? $nonces : [
            new Nonce('existing'),
            new Nonce('expired', Carbon::now()->addHour()),
        ];

        return new class ($nonces, $withAutomaticFind) implements NonceRepositoryInterface
        {
            /** @var NonceInterface[] */
            private $nonces;

            /** @var bool */
            private $withAutomaticFind;

            public function __construct(array $nonces, bool $withAutomaticFind)
            {
                foreach ($nonces as $nonce) {
                    $this->add($nonce);
                }

                $this->withAutomaticFind = $withAutomaticFind;
            }

            public function add(NonceInterface $nonce): self
            {
                $this->nonces[$nonce->getValue()] = $nonce;

                return $this;
            }

            public function find(string $value): ?NonceInterface
            {
                if ($this->withAutomaticFind) {
                    return current($this->nonces);
                }

                return $this->nonces[$value] ?? null;
            }

            public function save(NonceInterface $nonce): void
            {
                return;
            }
        };
    }

    private function getRegistrationRepositoryFromClaims($claims)
    {
        $registration = LtiPlatform::where('issuer', $claims->get('iss'))
                                ->where('client_id', $claims->get('aud')[0])
                                ->first();
        if (!$registration) {
            return "Your platform is not registered yet";
        }

        $deployment =LtiPlatformDeployment::where('platform_id', $registration->id)
                    ->where('deployment_id', $claims->get(LtiMessagePayloadInterface::CLAIM_LTI_DEPLOYMENT_ID))
                    ->first();
        if (!$deployment) {
            $deployment = LtiPlatformDeployment::create([
                'platform_id'   => $registration->id,
                'deployment_id'   => $claims->get(LtiMessagePayloadInterface::CLAIM_LTI_DEPLOYMENT_ID),
            ]);
        }

        $tool = $this->makeTool($registration->tool);
        $platform = $this->makePlatform($registration);
    
        $registrationInterface = new Registration(
            $registration->issuer,
            $registration->client_id,
            $platform,
            $tool,
            [$deployment->deployment_id],
            null,
            $this->toolKeyChain($registration->tool),
            $registration->public_keyset_url,
            $registration->tool->public_keyset_url,
        );
        return $this->createRegistrationRepository([$registrationInterface]);
    }
}
