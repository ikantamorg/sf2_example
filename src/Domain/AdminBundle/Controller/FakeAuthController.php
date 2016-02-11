<?php
/**
 * User: alkuk
 * Date: 04.04.14
 * Time: 15:54
 */

namespace Domain\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Domain\CoreBundle\Controller\CoreController;
use Domain\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FakeAuthController extends CoreController
{

    /**
     * @ParamConverter("user", class="CoreBundle:User")
     */
    public function loginAction(Request $request, User $user)
    {

        $firewall = 'main';

        $session = $request->getSession();

        $newToken = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());

        $session->set('_security_'.$firewall, serialize($newToken));

        if ($user->hasRole(User::ROLE_EXPERT)) {
            return $this->redirectPath('expert_profile_edit');
        } elseif ($user->hasRole(User::ROLE_CANDIDATE)) {
            return $this->redirectPath('candidate_profile_edit');
        }
    }
}
