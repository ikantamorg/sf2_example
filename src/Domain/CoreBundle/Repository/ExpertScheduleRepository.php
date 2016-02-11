<?php

namespace Domain\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ExpertScheduleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExpertScheduleRepository extends EntityRepository
{

    /**
     * Get schedule by expert ids and day numbers as array
     *
     * @param array $expertIds
     * @param $dayNums
     *
     * @return array
     */
    public function getArrayByExpertIds(array $expertIds, $dayNums)
    {
        $qb = $this->createQueryBuilder('schedule');
        $qb->addSelect('expert.id as expert_id')
            ->innerJoin('schedule.expert', 'expert')
            ->where(
            $qb->expr()->andX(
                $qb->expr()->in('schedule.expert', ':expertIds'),
                $qb->expr()->in('schedule.day', ':dayNums')
            )
        )->setParameters([
                    'expertIds' => $expertIds,
                    'dayNums' => $dayNums
                ])
        ;

        return $qb->getQuery()->getArrayResult();
    }
}