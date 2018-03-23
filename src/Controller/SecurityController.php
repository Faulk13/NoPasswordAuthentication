<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ConnectionProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Rémi Marcherat<rmarcherat@prestaconcept.net>
 */
class SecurityController extends Controller
{
    /**
     * @var ConnectionProcessor
     */
    private $connectionProcessor;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param ConnectionProcessor   $connectionProcessor
     * @param UserRepository        $userRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        ConnectionProcessor $connectionProcessor,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->connectionProcessor = $connectionProcessor;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        if (!$request->isMethod('post')) {
            return $this->render('security/login.html.twig');
        }

        $user = $this->userRepository->findOneBy(['email' => $request->request->get('email')]);
        if ($user instanceof User) {
            $this->connectionProcessor->createTokenProcess($user);
        }

        return $this->render('waiting/index.html.twig');
    }

    public function connectAction(Request $request)
    {
        $user = $this->userRepository->findOneBy(['connectionToken' => $request->query->get('token')]);

        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        if ($user->isTokenExpired(intval($this->getParameter('lifetime_token')))) {
            return $this->redirectToRoute('login');
        }

        $this->connectionProcessor->connectionProcess($user);

        $token = new UsernamePasswordToken($user, null, 'default', $user->getRoles());
        $this->tokenStorage->setToken($token);

        return $this->redirectToRoute('secured_area');
    }
}
