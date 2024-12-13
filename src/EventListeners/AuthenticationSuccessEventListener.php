<?php

namespace App\EventListeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(Events::AUTHENTICATION_SUCCESS)]
class AuthenticationSuccessEventListener
{
    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $event->setData(
            [
                'user' => array_merge(
                    $event->getData(),
                    [
                        'bio' => $user->getBio(),
                        'email' => $user->getEmail(),
                        'image' => $user->getImage(),
                        'username' => $user->getUsername(),
                    ],
                ),
            ],
        );
    }
}