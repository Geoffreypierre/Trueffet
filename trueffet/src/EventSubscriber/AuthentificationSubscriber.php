<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthentificationSubscriber
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {

    }

    #[AsEventListener]
    public function onLoginSuccessEvent(LoginSuccessEvent $loginSuccessEvent): void {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add('success', 'Connexion réussie !');
    }

    #[AsEventListener]
    public function onLoginFailureEvent(LoginFailureEvent $loginFailureEvent): void {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add('error', 'Login et/ou mot de passe incorrect !');
    }

    #[AsEventListener]
    public function onLogoutEvent(LogoutEvent $logoutEvent): void {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add('success', 'Déconnexion réussie !');
    }


}