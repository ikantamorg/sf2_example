<?php
/**
 * User: Dred
 * Date: 18.12.13
 * Time: 12:31
 */
namespace Domain\CoreBundle\Service\MQ;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Domain\CoreBundle\Entity\Appointment;
use JMS\JobQueueBundle\Entity\Job;
use Psr\Log\LoggerInterface;
use Resque;
use Exception;

class JobManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $env;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->env = $this->container->get('kernel')->getEnvironment()?:'prod';
        $this->entity = 'JMSJobQueueBundle:Job';

        $this->logger = $this->container->get('logger');
    }

    /**
     * Add emails sent command to job queue
     */
    public function regularCloseOverdueAppointments()
    {
        $command = 'task:appointment:overdue:close';
        $this->addRegularJob($command);

    }

    /**
     * Add to job list - appointment's video processing
     *
     * @param Appointment $appointment
     *
     * @return $this
     */
    public function processAppointmentVideo(Appointment $appointment)
    {
        $job = new Job('task:appointment:video:process', [$appointment->getId()]);
        $job->setMaxRetries(100);

        $this->addJob($job);

        return $this;
    }

    /**
     * Add to job list - appointment's payment completion
     *
     * @param Appointment $appointment
     * @return $this
     */
    public function completeAppointmentPayment(Appointment $appointment)
    {
        $job = new Job('task:appointment:payment:complete', [$appointment->getId()]);
        $job->setMaxRetries(100);

        $this->addJob($job);

        return $this;
    }

    /**
     * Add to job list - appointment's payment refund
     *
     * @param Appointment $appointment
     *
     * @return $this
     */
    public function refundAppointmentPayment(Appointment $appointment)
    {
        $job = new Job('task:appointment:payment:refund', [$appointment->getId()]);
        $job->setMaxRetries(100);

        $this->addJob($job);

        return $this;
    }

    /**
     * Add emails sent command to job queue
     */
    public function regularSentEmails()
    {
        $command = 'swiftmailer:spool:send';
        $this->addRegularJob($command);

    }

    /**
     * Add to job list -  appointment reminder
     */
    public function regularAppointmentReminder()
    {
        $command = 'task:appointment:reminder';
        $this->addRegularJob($command);
    }

    /**
     * Add to job list -  appointment auto reminder
     */
    public function regularAppointmentAutoReminder()
    {
        $command = 'task:appointment:reminder:auto';
        $this->addRegularJob($command);
    }

    /**
     * Add to job list -  appointment completion
     */
    public function regularAppointmentComplete()
    {
        $command = 'task:appointment:complete';
        $this->addRegularJob($command);
    }

    /**
     * Add to job list -  update appointment's video status
     */
    public function updateVideoStatuses()
    {
        $command = 'task:appointment:video:status:update';
        $queue = 'update_video_status';
        $max = 10;
        $i = 0;

        try {

            while ($i < $max && ($redisJob = Resque::reserve($queue))) {
                $videoPath = $redisJob->getArguments();
                if (empty($videoPath)) {
                    continue;
                }
                $job = new Job($command, [$videoPath]);
                $job->setMaxRetries(100);
                $this->addJob($job);

                $i++;
            }

        } catch(Exception $e) {
            $this->logger->error($e->getMessage(), array(
                'location' => 'jobManager:updateVideoStatuses',
                'stack_trace' => $e->getTraceAsString(),
            ));
        }

    }

    /**
     * Create new job if not exist
     *
     * @param $command
     * @param array $args
     */
    protected function addRegularJob($command, $args = [])
    {
        $query = $this->em->createQuery("SELECT COUNT(j.id) FROM ".$this->entity." j WHERE j.command = :command AND j.state IN (:states)")
            ->setParameter('command', $command)
            ->setParameter('states', [
                    Job::STATE_NEW,
                    Job::STATE_PENDING,
                    Job::STATE_RUNNING,
                ])
        ;
        $count = (int)$query->getSingleScalarResult();

        if ($count) {
            return;
        }

        $job = new Job($command, $args);

        $this->addJob($job);
    }

    /**
     * Add job to queue
     *
     * @param Job $job
     *
     * @return $this
     */
    protected function addJob(Job $job)
    {
        $this->em->persist($job);
        $this->em->flush($job);

        return $this;
    }

    /**
     * Get job repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepo()
    {
        return $this->em->getRepository($this->entity);
    }

}
 