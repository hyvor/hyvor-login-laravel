<?php

namespace Hyvor\Internal\Bundle\Security;

use Hyvor\Internal\Auth\Auth;
use Hyvor\Internal\Auth\AuthUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class HyvorAuthenticator extends AbstractAuthenticator
{

    public function __construct(
        private Auth $auth
    ) {
    }


    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $cookie = $request->cookies->get(Auth::HYVOR_SESSION_COOKIE_NAME);

        if ($cookie === null) {
            throw new AuthenticationException('Hyvor session cookie not found');
        }

        // $user = Auth::check();
        $username = 'test';
        $user = $this->auth->check();

        return new SelfValidatingPassport(
            new UserBadge(
                $username,
                function (string $username): ?AuthUser {
                    return null;
                    return AuthUser::fromArray([
                        'id' => 1,
                        'username' => $username,
                        'name' => 'Test User',
                        'email' => 'test@hyvor.com'
                    ]);
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}