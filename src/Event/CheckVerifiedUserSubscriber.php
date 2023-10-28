<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10]
        ];
    }

    public static function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();

        $user = $passport->getUser();

        if (!$user instanceof User) {
            throw new \Exception('Unexpected user type');
        }

        if (!$user->getVerifiedAt()) {
            throw new CustomUserMessageAccountStatusException('Please verify your account before loggin in.');
        }
    }
}
