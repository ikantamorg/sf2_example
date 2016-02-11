<?php
namespace Domain\CoreBundle\Security\Core\User;

use Doctrine\DBAL\DBALException;
use Domain\CoreBundle\Entity\ExpertRequest;
use Domain\CoreBundle\Entity\User;
use Domain\CoreBundle\Util\StringUtils;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Domain\CoreBundle\Service\Socializer\SocializerException;

class FOSUBUserProvider extends BaseClass
{

    protected $container;
    protected $em;

    public function __construct(
        UserManagerInterface $userManager,
        array $properties,
        \Symfony\Component\DependencyInjection\Container $container
    ) {
        parent::__construct($userManager, $properties);

        $this->container = $container;
        $this->em = $this->container->get('doctrine')
            ->getManager();

    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {

        $property = $this->getProperty($response);
        $username = $response->getEmail();

        //@TODO - ask poul for this feature!!!
        /*if ($username != $user->getEmail()) {
            throw new AccessDeniedException();
        }*/

        //on connect - get the access token and the user ID
        $service = $this->checkServiceForExpert($response->getResourceOwner()->getName());
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
/*        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }*/

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);

        if ($response->getResourceOwner()->getName() == 'expert_linkedin') {

            $this->makeExpertRequest($user);
            $this->container->get('session')->getFlashBag()
                ->add('success', 'Your request was successfully sent to Admin.');
        }

    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        $username = $response->getEmail();

        $name = explode(' ', $response->getRealName());

        $firstName = isset($name[0]) ? $name[0] : $username;
        $lastName = isset($name[1]) ? $name[1] : $username;

        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        $service = $this->checkServiceForExpert($response->getResourceOwner()->getName());

        //when the user is registrating
        if (null === $user) {


            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';

            $plainPassword = StringUtils::generatePassword();

            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());

            $user->setUsername($username);
            $user->setEmail($username);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setTerms(true);
            $user->setPlainPassword($plainPassword);
            $user->setEnabled(true);
            $user->addRole(User::ROLE_CANDIDATE);

            $linkedinAPI = $this->container->get('socializer_linkedin');
            $linkedinAPI->setUser($user);

            try{
                $profileLink = $linkedinAPI->getProfileLink();
            } catch(SocializerException $e){
                $session = $this->container->get('session');

                $session
                    ->getFlashBag()
                    ->add('error', $e->getMessage());

                $session->invalidate();
            }

            $user->setLinkedinProfileLink($profileLink);

            $validator = $this->container->get('validator');
            $errors = $validator->validate($user, array('registration'));

            if (count($errors) > 0) {

                foreach ($errors as $_error) {
                    $this->container->get('session')
                        ->getFlashBag()
                        ->add('error', $_error);
                }

                throw new AccountNotLinkedException(sprintf("Errors occured", $username));

            }

            $manager = $this->container->get('fos_user.user_manager');


            if ($this->checkIsExpertRegistration($response->getResourceOwner()->getName())) {
                $this->makeExpertRequest($user);
            }

            $manager->updateUser($user);
            $this->em->flush();

            $this->container->get('domain.core.event.listener')->dispatch('registered_by_linkedin', 'user', $user);


            $this->container->get('session')
                ->getFlashBag()
                ->add('success', 'Your profile was successfully registered!');

            return $user;
        }

        //if user exists - go with the HWIOAuth way
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        if (null === $user) {
            throw new AccountNotLinkedException(sprintf("User '%s' not found.", $username));
        }

        $setter = 'set' . ucfirst($service) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

    private function checkIsExpertRegistration($service)
    {
        $serviceArr = explode('_', $service);
        $isExpert = count($serviceArr) > 1;

        return $isExpert;
    }

    /**
     * Used to get service name (like linkedin \ facebook \ etc) from service data
     * Data incoming in format 'candidate_linkedin' or 'expert_linkedin'
     * We need to remove 'blabla_' prefix
     *
     * @param $service
     * @return mixed
     */
    private function checkServiceForExpert($service)
    {

        $serviceArr = explode('_', $service);
        $isExpert = $this->checkIsExpertRegistration($service);

        if ($isExpert) {
            $service = $serviceArr[1];
        }

        return $service;
    }

    private function makeExpertRequest(User $user)
    {
        return $this->container->get('expert_manager')
            ->setUser($user)
            ->sentActiveRequest()
            ;

    }
}
