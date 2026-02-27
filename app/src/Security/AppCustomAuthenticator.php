<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Autenticador personalizado para el inicio de sesión de la aplicación.
 * Gestiona la validación de credenciales, la creación del pasaporte de seguridad
 * y las redirecciones tras un inicio de sesión exitoso.
 */
final class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * Inicializa el autenticador inyectando las dependencias necesarias.
     *
     * @param UrlGeneratorInterface $urlGenerator Servicio para generar rutas dentro de la aplicación.
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * Procesa la petición de inicio de sesión y crea un pasaporte con las credenciales del usuario.
     *
     * @param Request $request Petición HTTP entrante con los datos del formulario.
     * @return Passport El pasaporte de seguridad con insignias (badges) como CSRF y RememberMe.
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Maneja la redirección del usuario una vez que se ha autenticado correctamente.
     *
     * @param Request $request Petición HTTP actual.
     * @param TokenInterface $token El token de seguridad generado tras el login.
     * @param string $firewallName El nombre del firewall actual.
     * @return Response|null Respuesta HTTP de redirección al destino previo o a la ruta principal.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Devuelve la URL absoluta de la página de inicio de sesión.
     *
     * @param Request $request Petición HTTP actual.
     * @return string La ruta generada para el login.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
