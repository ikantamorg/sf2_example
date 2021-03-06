<?php

namespace Domain\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Domain\CoreBundle\Entity\Appointment;
use Domain\CoreBundle\Entity\User;
use Domain\CoreBundle\Entity\Expert;

/**
 * ReviewRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReviewRepository extends EntityRepository
{
    /**
     * Find review by appointment and candidate
     * 
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @param \Domain\CoreBundle\Entity\User $candidate
     * @return \Domain\CoreBundle\Entity\Review
     */
    public function findByAppointmentAndCandidate(Appointment $appointment, User $candidate)
    {
        return $this->findOneBy([
            'appointment' => $appointment,
            'candidate' => $candidate,
        ]);
    }

    /**
     * Find review by appointment and expert
     * 
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return \Domain\CoreBundle\Entity\Review
     */
    public function findByAppointmentAndExpert(Appointment $appointment, Expert $expert)
    {
        return $this->findOneBy([
            'appointment' => $appointment,
            'expert' => $expert,
        ]);
    }

    /**
     * Find all expert review ordered by date desc
     * 
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return PersistentCollection
     */
    public function findAllOrderedByCreatedAt(Expert $expert)
    {
        return $this->findBy([
            'expert' => $expert,
        ], [
            'createdAt' => 'DESC',
        ]);
    }
}
