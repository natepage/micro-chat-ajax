<?php

namespace AppBundle\Chat;

use AppBundle\Entity\Message;
use AppBundle\Entity\UserStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatManager
{
    const USER_STATUS_ONLINE = 'online';
    const USER_STATUS_ABSENT = 'absent';
    const TIME_BEFORE_ABSENT = 2;
    const TIME_BEFORE_AUTO_LOGOUT = 30;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $messageRepository = 'AppBundle:Message';

    /**
     * @var string
     */
    private $statusRepository = 'AppBundle:UserStatus';

    /**
     * @var string
     */
    private $messageTemplate = ':messages:conversation.html.twig';

    /**
     * @var string
     */
    private $emptyTemplate = ':messages:no_message.html.twig';

    /**
     * @var string
     */
    private $notificationTemplate = ':messages:notification.html.twig';

    /**
     * @var string
     */
    private $statusUserTemplate = ':default:status_user.html.twig';

    public function __construct(EntityManagerInterface $em, EngineInterface $templating, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->tokenStorage = $tokenStorage;
    }

    public function send(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $message = new Message();
        $message->setContent($request->request->get('content'));
        $message->setUser($user);

        $this->em->persist($message);
        $this->em->flush();

        $this->updateUserStatus($user);

        return $this->getMessages();
    }

    public function getMessages()
    {
        $render = '';
        $messages = $this->em->getRepository($this->messageRepository)->findLastsMessages();

        foreach($messages as $message){
            $render .= $this->templating->render($this->messageTemplate, array('message' => $message));
        }

        if($render == ''){
            $render = $this->templating->render($this->emptyTemplate);
        }

        return $render;
    }

    public function washConversation()
    {
        $messages = $this->em->getRepository($this->messageRepository)->findAll();

        foreach($messages as $message){
            $this->em->remove($message);
        }

        $this->em->flush();

        return $this->templating->render($this->notificationTemplate, array(
            'type' => 'success',
            'content' => 'Conversation nettoyée.'
        ));
    }

    public function updateUserStatus(UserInterface $user)
    {
        $status = $this->em->getRepository($this->statusRepository)->findOneBy(array('username' => $user->getUsername()));

        if(null === $status){
            $status = new UserStatus();
            $status->setUsername($user->getUsername());
        }

        $status->setLastMessage(new \DateTime());
        $status->setStatus(self::USER_STATUS_ONLINE);

        $this->em->persist($status);
        $this->em->flush();
    }

    public function removeUserStatus(UserInterface $user)
    {
        $status = $this->em->getRepository($this->statusRepository)->findOneBy(array('username' => $user->getUsername()));

        if(!null === $status){
            $this->em->remove($status);
            $this->em->flush();
        }
    }

    public function getUsers()
    {
        $users = $this->findUsersWithRefreshListStatus();
        $render = array(
            'render' => '',
            'notifications' => array()
        );

        foreach($users['status'] as $status){
            $render['render'] .= $this->templating->render($this->statusUserTemplate, array('status' => $status));
        }

        foreach($users['connected'] as $connected){
            $render['notifications'][] = $this->templating->render($this->notificationTemplate, array(
                'type' => 'info',
                'content' => $connected . ' has joined conversation'
            ));
        }

        foreach($users['deconnected'] as $deconnected){
            $render['notifications'][] = $this->templating->render($this->notificationTemplate, array(
                'type' => 'info',
                'content' => $deconnected . ' has left conversation'
            ));
        }

        return array(
            'number' => count($users['status']),
            'render' => $render['render'],
            'notifications' => $render['notifications']
        );
    }

    private function findUsersWithRefreshListStatus()
    {
        $users = array(
            'status' => array(),
            'connected' => array(),
            'deconnected' => array()
        );

        $listStatus = $this->em->getRepository($this->statusRepository)->findAll();
        $now = new \DateTime();

        foreach($listStatus as $status){
            $diff = $now->diff($status->getLastMessage())->i;

            if($status->getJustConnected()){
                $users['connected'][] = $status->getUsername();
                $status->setJustConnected(false);
            }

            if($diff > self::TIME_BEFORE_ABSENT && $diff < self::TIME_BEFORE_AUTO_LOGOUT){
                $status->setStatus(self::USER_STATUS_ABSENT);
            } elseif($diff > self::TIME_BEFORE_AUTO_LOGOUT){
                $users['deconnected'][] = $status->getUsername();
                $this->em->remove($status);
            }

            if(!in_array($status->getUsername(), $users['deconnected'])){
                $users['status'][] = $status;
            }
        }

        $this->em->flush();

        return $users;
    }
}