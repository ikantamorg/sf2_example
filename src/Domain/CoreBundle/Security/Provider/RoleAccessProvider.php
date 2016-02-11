<?php
/**
 * User: Dred
 * Date: 25.11.13
 * Time: 16:16
 */

namespace Domain\CoreBundle\Security\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class RoleAccessProvider extends DaoAuthenticationProvider
{

    protected $allowedRoles;

    /**
     * {@inheritdoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {

        parent::checkAuthentication($user, $token);

        $currentUser = $token->getUser();

        if (!$currentUser instanceof UserInterface) {
            foreach ($user->getRoles() as $role) {
                if (in_array($role, $this->allowedRoles)) {
                    return;
                }
            }

            throw new BadCredentialsException('You are not allowed to access this section.');
        }

    }

    public function setAllowedRoles(array $roles)
    {
        $this->allowedRoles = $roles;
    }

}
 