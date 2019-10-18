<?php

namespace App\Listeners;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\Video;
use App\Entity\User;


class RegisterListener
{

    public function __construct(\Twig_Environment $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $entityManager = $args->getObjectManager();

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo($entity->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/register.html.twig',
                    [
                        'name' => $entity->getName(),
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);

    }
}

