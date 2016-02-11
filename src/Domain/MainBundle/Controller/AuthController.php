<?php
/**
 * User: Dred
 * Date: 14.10.13
 * Time: 16:31
 */

namespace Domain\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Domain\CoreBundle\Util\StringUtils;
use Domain\CoreBundle\Entity\User;
use Domain\MainBundle\Form\LoginType;
use Domain\MainBundle\Form\UserType;
use Domain\MainBundle\Form\ForgotPasswordType;
use Domain\MainBundle\Form\ResetPasswordType;

class AuthController extends Controller
{

    /**
     * Default login action
     *
     * @access public
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $form = $this->createForm(
            new LoginType(),
            [
                'email' => $session->get(SecurityContext::LAST_USERNAME)
            ]
        );
        return $this->render(
            'MainBundle:Auth/Login:index.html.twig',
            array(
                'error' => $error,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Candidate manual registration
     *
     * @access public
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registrationAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {

                $userManager = $this->get('fos_user.user_manager');
                $user->setUsername($user->getEmail());
                $user->addRole("ROLE_CANDIDATE");
                $user->setEnabled(true);
                $userManager->updateUser($user, true);

                $this->get('domain.core.event.listener')->dispatch('registered', 'user', $user);

                $this->get('session')->getFlashBag()->add('success', 'Your profile was successfully registered!');
                return $this->redirect($this->generateUrl('login_page'));
            } else {
                $this->get('session')
                    ->getFlashBag()
                    ->add('error', 'Something goes wrong. Please fix errors and try again');
            }
        }
        return $this->render(
            'MainBundle:Auth/Registration:index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    public function registrationExpertAction()
    {
        return $this->render('MainBundle:Auth/Registration:expert.html.twig');
    }

    /**
     * Render the 'forgotten password' form
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function forgotPasswordAction(Request $request)
    {

        $form = $this->createForm(new ForgotPasswordType());
        $session = $request->getSession();
        $errors = [];
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $email = $data['email'];
                $userManager = $this->get('fos_user.user_manager');
                $user = $userManager->findUserByEmail($email);
                if (null === $user) {
                    $session->getFlashBag()->add('error', 'The email address "' . $email . '" does not exist.');
                    return $this->redirect($this->generateUrl('forgotten_password'), 301);
                }
                if (null === $user->getConfirmationToken()) {
                    /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                    $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                    $user->setConfirmationToken($tokenGenerator->generateToken());
                    $userManager->updateUser($user);
                }
                $this->get('domain.core.event.listener')->dispatch('password_restore', 'user', $user);

                return $this->redirect($this->generateUrl('forgot_password_reset_success'));
            } else {
                foreach ($form->all() as $child) {
                    foreach ($child->getErrors() as $error) {
                        array_push($errors, $error->getMessage());
                    }
                }
            }
        }
        return $this->render(
            'MainBundle:Auth/ForgotPassword:index.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $errors
            ]
        );

    }

    public function passwordResetAction(Request $request, $token)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByConfirmationToken($token);
        if (null === $user) {
            $this->createNotFoundException(
                sprintf('The user with "confirmation token" does not exist for value "%s"', $token)
            );
        }
        $form = $this->createForm(new ResetPasswordType, $user);
        if ('POST' === $request->getMethod()) {
            $form->submit($request);
            if ($form->isValid()) {
                $user->setConfirmationToken(null);
                $userManager->updateUser($user);
                $this->get('session')
                    ->getFlashBag()
                    ->add('success', 'You password was successfully updated.');

                return $this->redirect($this->generateUrl('login_page'));
            }
        }

        return $this->render(
            'MainBundle:Auth/ForgotPassword:passwordReset.html.twig',
            [
                'form' => $form->createView(),
                'token' => $token
            ]
        );
    }

    /**
     * Render message 'your password was send to your email'
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetSuccessAction()
    {
        return $this->render('MainBundle:Auth/ForgotPassword:success.html.twig');
    }
}
