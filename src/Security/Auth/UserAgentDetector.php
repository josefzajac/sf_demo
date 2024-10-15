<?php declare(strict_types=1);

namespace Document\Security\Auth;

use Document\Security\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UserAgentDetector implements AuthenticatorInterface
{
    public function __construct(
        private readonly DummyProvider $provider,
        private readonly string $appCompany,
    ) {}

    public function authenticate(Request $request): Passport
    {
        if (!$this->detect($request->headers->get('User-Agent', ''), $this->appCompany)) {
            throw new UserNotFoundException('Wrong User-Agent');
        }

        return new SelfValidatingPassport(new UserBadge('1', $this->provider->returnDummy(...)));
    }

    public function detect(string $ua, string $appCompany): bool
    {
        return match (true) {
            str_contains($ua, $appCompany) => true,
            str_contains($ua, 'Symfony') => true,
            default => false,
        };
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new NullToken();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }
}
