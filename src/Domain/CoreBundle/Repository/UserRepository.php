<?php

namespace Domain\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Domain\CoreBundle\Entity\User;

class UserRepository extends EntityRepository
{
    /**
     * Get query builder with roles statement
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQBRolesContains($contains)
    {
        $qb = $this->createQueryBuilder('user');

        $qb->where($qb->expr()->like('user.roles', ':role'))
            ->setParameter('role', $contains)
        ;

        return $qb;
    }

    /**
     * Get query builder of All candidates
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQBOfCandidates()
    {
        $qb = $this->getQBRolesContains('%'.User::ROLE_CANDIDATE.'%');

        /**
         * used to improve performance
         */
        $qb->addSelect(['expert', 'notificationOptions', 'avatar'])
            ->leftJoin('user.expert', 'expert')
            ->leftJoin('user.notificationOptions', 'notificationOptions')
            ->leftJoin('user.avatar', 'avatar')
        ;

        return $qb;
    }

    /**
     * Get query builder of All experts
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQBOfExperts()
    {
        $qb = $this->getQBRolesContains('%'.User::ROLE_EXPERT.'%');

        /**
         * used to improve performance
         */
        $qb->addSelect(['expert', 'billing', 'notificationOptions', 'avatar', 'additional_info'])
            ->innerJoin('user.expert', 'expert')
            ->leftJoin('user.notificationOptions', 'notificationOptions')
            ->leftJoin('user.avatar', 'avatar')
            ->leftJoin('expert.billing', 'billing')
            ->leftJoin('expert.additional_info', 'additional_info')
        ;


        return $qb;
    }
} 