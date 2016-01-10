<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Chat\ChatManager;

/**
 * UserStatus
 *
 * @ORM\Table(name="user_status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserStatusRepository")
 */
class UserStatus
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastMessage", type="datetime")
     */
    private $lastMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var boolean
     *
     * @ORM\Column(name="justConnected", type="boolean", options={"default": true})
     */
    private $justConnected;

    /**
     * @var boolean
     *
     * @ORM\Column(name="justDeconnected", type="boolean", options={"default": false})
     */
    private $justDeconnected;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    public function __construct()
    {
        $this->lastMessage = new \DateTime();
        $this->status = ChatManager::USER_STATUS_ONLINE;
        $this->justConnected = true;
        $this->justDeconnected = false;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lastMessage
     *
     * @param \DateTime $lastMessage
     *
     * @return UserStatus
     */
    public function setLastMessage($lastMessage)
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * Get lastMessage
     *
     * @return \DateTime
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * Set username
     *
     * @param $username
     *
     * @return UserStatus
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set status
     *
     * @param $status
     *
     * @return UserStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set justConnected
     *
     * @param $justConnected
     *
     * @return UserStatus
     */
    public function setJustConnected($justConnected)
    {
        $this->justConnected = $justConnected;

        return $this;
    }

    /**
     * Get justConnected
     *
     * @return bool
     */
    public function getJustConnected()
    {
        return $this->justConnected;
    }

    /**
     * Set justDeconnected
     *
     * @param $justDeconnected
     *
     * @return UserStatus
     */
    public function setJustDeconnected($justDeconnected)
    {
        $this->justDeconnected = $justDeconnected;

        return $this;
    }

    /**
     * Get justDeconnected
     *
     * @return bool
     */
    public function getJustDeconnected()
    {
        return $this->justDeconnected;
    }
}

