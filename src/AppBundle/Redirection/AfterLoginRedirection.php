<?php

namespace AppBundle\Redirection;

use AppBundle\Chat\ChatManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \AppBundle\Chat\ChatManager
     */
    private $chatManager;

    public function __construct(RouterInterface $router, ChatManager $chatManager)
    {
        $this->router = $router;
        $this->chatManager = $chatManager;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $this->chatManager->updateUserStatus($token->getUser());

        return new RedirectResponse($this->router->generate('homepage'));
    }
}