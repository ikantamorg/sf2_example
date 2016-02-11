<?php

namespace Domain\CoreBundle\Service\Socializer;

use Domain\CoreBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class SocializerAbstract
{
    /**
     * Used entity
     *
     * @var \Domain\CoreBundle\Entity\User
     */
    protected $user;

    /**
     * Access token for social
     *
     * @var string
     */
    protected $token;

    public function __construct()
    {
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        $this->setAccessToken($user);
    }

    protected function setAccessToken(User $user)
    {
    }

    public function getToken()
    {
        return $this->token;
    }

    protected function apiCall()
    {
    }
}
