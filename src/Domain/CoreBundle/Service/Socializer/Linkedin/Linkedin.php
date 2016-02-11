<?php

namespace Domain\CoreBundle\Service\Socializer\Linkedin;

use Domain\CoreBundle\Entity\User;
use Domain\CoreBundle\Service\Socializer\SocializerAbstract;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Domain\CoreBundle\Service\Socializer\SocializerException;

class Linkedin extends SocializerAbstract
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Used to get link to linkedin profile
     *
     * @access public
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return string
     */
    public function getProfileLink()
    {
        $resource = '/v1/people/~';

        $apiResponse = $this->apiCall($resource);

        if (!isset($apiResponse->siteStandardProfileRequest)) {

            throw new NotFoundHttpException('Cant get profile data');

        }

        return $apiResponse->siteStandardProfileRequest->url;
    }

    /**
     * Used to get user profile image
     *
     * @access public
     * @return bool
     */
    public function getAvatarUrl()
    {
        $resource = '/v1/people/~:(picture-url)';

        $apiResponse = $this->apiCall($resource);

        if (!isset($apiResponse->pictureUrl)) {
            return null;
        }

        return $apiResponse->pictureUrl;
    }

    public function getProfileInfo()
    {
        $resource = '/v1/people/~:(first-name,last-name,headline,picture-url)';

        $apiResponse = $this->apiCall($resource);

        if (!isset($apiResponse)) {
            return null;
        }

        $default_values = [
            'firstName' => '',
            'lastName' => '',
            'headline' => '',
            'pictureUrl' => '',
            'headline' => ''
        ];
        return array_merge($default_values, (array)$apiResponse);
    }

    /**
     * Used to get user skills
     *
     * @access public
     * @return array|bool
     */
    public function getSkills()
    {
        $resource = '/v1/people/~:(skills)';

        $apiResponse = $this->apiCall($resource);

        $skills = [];

        if (!isset($apiResponse->skills)) {
            return $skills;
        }

        foreach ($apiResponse->skills->values as $_skill) {
            array_push($skills, ['name' => $_skill->skill->name]);
        }

        return $skills;
    }

    /**
     * Used to get user Birthday date from LinkedIn
     *
     * @access public
     * @return bool|\DateTime
     */
    public function getBirthday()
    {
        $resource = '/v1/people/~:(date-of-birth)';

        $apiResponse = $this->apiCall($resource);

        if (!isset($apiResponse->dateOfBirth)) {
            return null;
        }

        $time =  $apiResponse->dateOfBirth->day.'-'.$apiResponse->dateOfBirth->month.'-'.
            $apiResponse->dateOfBirth->year;

        return \DateTime::createFromFormat('j-m-Y', $time);
    }

    /**
     * Used to get user interest
     *
     * @access public
     * @return bool
     */
    public function getInterests()
    {
        $resource = '/v1/people/~:(interests)';

        $apiResponse = $this->apiCall($resource);

        if (!isset($apiResponse->interests)) {
            return null;
        }

        return $apiResponse->interests;
    }

    /**
     * Used to get user educations
     *
     * @access public
     * @return array|bool
     */
    public function getEducations()
    {
        $resource = '/v1/people/~:(educations)';

        $apiResponse = $this->apiCall($resource);

        $educations = [];

        if (!isset($apiResponse->educations)) {
            return $educations;
        }

        foreach ($apiResponse->educations->values as $_educations) {
            array_push(
                $educations,
                [
                    'name' => $_educations->schoolName,
                    'activities' => isset($_educations->activities) ? $_educations->activities : null,
                    'degree' => isset($_educations->degree) ? $_educations->degree : null,
                    'fieldOfStudy' => isset($_educations->fieldOfStudy) ? $_educations->fieldOfStudy : null,
                    'startDate' => isset($_educations->startDate) ? $_educations->startDate->year : null,
                    'endDate' => isset($_educations->endDate) ? $_educations->endDate->year : null,
                    'description' => isset($_educations->notes) ? $_educations->notes : null
                ]
            );
        }

        return $educations;
    }

    /**
     * Used to get summary for profile
     *
     * @access public
     * @return array|bool
     */
    public function getSummary()
    {
        $resource = '/v1/people/~:(summary)';

        $apiResponse = $this->apiCall($resource);

        if (!isset($apiResponse->summary)) {
            return null;
        }

        return $apiResponse->summary;
    }

    /**
     * Used to get experiences
     *
     * @access public
     * @return array|bool
     */
    public function getExperiences()
    {
        $resource = '/v1/people/~:(positions)';

        $apiResponse = $this->apiCall($resource);

        $experiences = [];

        if (!isset($apiResponse->positions)) {
            return $experiences;
        }

        foreach ($apiResponse->positions->values as $_position) {
            if (empty($_position->company->name)) {
                continue;
            }
            array_push(
                $experiences,
                [
                    'name' => $_position->company->name,
                    'title' => $_position->title,
                    'description' => isset($_position->summary) ? $_position->summary : null,
                    'location' => isset($_position->location) ? $_position->location: null
                ]
            );
        }

        return $experiences;
    }

    /**
     * Get all user's info from linked in
     *
     * @return mixed|void
     */
    public function getAll()
    {
        $resource = '/v1/people/~:(first-name,last-name,headline,picture-url,date-of-birth,skills,interests,educations,summary,positions,location:(name,country:(code)),industry)';
        $apiResponse = $this->apiCall($resource);
        return $apiResponse;
    }

    /**
     * Set access token for selected user
     * Access token used in API calls
     *
     * @access protected
     * @param User $user
     * @throws \HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException
     */
    protected function setAccessToken(User $user)
    {
        $this->token = $user->getLinkedinAccessToken();

        //if token empty - linkedin account is not linked
        if (empty($this->token)) {
            throw new AccountNotLinkedException();
        }
    }

    /**
     * Send API call to linkedin
     *
     * @access public
     * @param string $resource
     * @return mixed|void
     */
    protected function apiCall($resource = '')
    {
        $params = [
            'oauth2_access_token' => $this->getToken(),
            'format' => 'json',
        ];

        // Need to use HTTPS
        $url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);

        // Tell streams to make a (GET, POST, PUT, or DELETE) request
        $context = stream_context_create(
            [
                'http' => [
                    'method' => 'GET',
                    'ignore_errors' => true
                ]
            ]
        );

        // Hocus Pocus
        $response = @file_get_contents($url, false, $context);

        if($response === false){
            throw new SocializerException('Cant get linkedin data!');
        }

        // Native PHP object, please
        $data =  json_decode($response);
        if(isset($data->errorCode)){
            throw new SocializerException($data->message);
        }

        return $data;
    }
}
