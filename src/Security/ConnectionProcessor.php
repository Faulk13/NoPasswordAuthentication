<?php

namespace App\Security;

use DateTime;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Environment;

/**
 * @author Rémi Marcherat<rmarcherat@prestaconcept.net>
 */
class ConnectionProcessor
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @param ObjectManager           $manager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param Environment             $twig
     * @param Swift_Mailer            $mailer
     */
    public function __construct(
        ObjectManager $manager,
        TokenGeneratorInterface $tokenGenerator,
        Environment $twig,
        Swift_Mailer $mailer
    ) {
        $this->manager = $manager;
        $this->tokenGenerator = $tokenGenerator;
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function createTokenProcess(User $user)
    {
        $token = $this->tokenGenerator->generateToken();

        $user->setConnectionAt(new DateTime);
        $user->setConnectionToken($token);

        $this->manager->persist($user);
        $this->manager->flush();

        $message = (new Swift_Message('Connection Link', [], 'mailer'))
            ->setFrom('test@test.com')
            ->setTo($user->getEmail())
            ->setContentType('text/html')
            ->setBody(
                $this->twig->render(
                    'mailer/admin/connection_link.html.twig', [
                        'token' => $token
                    ]
                )
            )
        ;

        $this->mailer->send($message);

        return $token;
    }

    /**
     * @param User $user
     */
    public function connectionProcess(User $user)
    {
        $user->setConnectionAt(null);
        $user->setConnectionToken(null);

        $this->manager->persist($user);
        $this->manager->flush();
    }
}
