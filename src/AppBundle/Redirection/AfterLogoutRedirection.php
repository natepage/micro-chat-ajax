<?php

namespace AppBundle\Redirection;

use AppBundle\Chat\ChatManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AfterLogoutRedirection implements LogoutSuccessHandlerInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \AppBundle\Chat\ChatManager
     */
    private $chatManager;

    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage, ChatManager $chatManager)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->chatManager = $chatManager;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $this->chatManager->removeUserStatus($this->tokenStorage->getToken()->getUser());

        return new RedirectResponse($this->router->generate('homepage'));
    }
}