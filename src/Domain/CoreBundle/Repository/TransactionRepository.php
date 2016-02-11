<?php

namespace Domain\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Domain\CoreBundle\Entity\Expert;
use Domain\CoreBundle\Entity\User;
use Domain\CoreBundle\Entity\Transaction;

/**
 * TransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends EntityRepository
{
    /**
     * Find the one and only transaction by pay key
     * 
     * @param string $payKey
     * @return \Domain\CoreBundle\Entity\Transaction
     */
    public function findOneByPayKey($payKey)
    {
        return $this->findOneBy([
            'payKey' => $payKey,
        ]);
    }

    /**
     * Get all expert's transactions filtered
     * 
     * @param Domain\CoreBundle\Entity\Expert $expert
     * @param array $filters - array with non-parsed values
     * @param string $defaultStatus - if status is default - dont search by status
     * @return array
     */
    public function findAllFilteredByExpert(Expert $expert, $filters = [], $defaultStatus = 'all')
    {
        // filters have date from, date to and status, they will be added straight to parameters
        // add expert to them
        $parameters = ['expert' => $expert] + $filters;

        return $this->findAllFiltered($parameters, $defaultStatus);
    }

    /**
     * Get all candidate's transactions filtered
     * 
     * @param Domain\CoreBundle\Entity\User $user
     * @param array $filters - array with non-parsed values
     * @param string $defaultStatus - if status is default - dont search by status
     * @return array
     */
    public function findAllFilteredByCandidate(User $candidate, $filters = [], $defaultStatus = 'all')
    {
        // filters have date from, date to and status, they will be added straight to parameters
        // add candidate to them
        $parameters = ['candidate' => $candidate] + $filters;

        return $this->findAllFiltered($parameters, $defaultStatus);
    }

    /**
     * Get all transactions filtered
     * 
     * @param array $parameters - params ready to be inserted in query
     * @param string $defaultStatus - if status is default - dont search by status
     * @return array - with results
     */
    protected function findAllFiltered($parameters = [], $defaultStatus = 'all')
    {
        $builder = $this->createQueryBuilder('t'); // transactions

        if (isset($parameters['expert'])) {
            $builder->where('t.expert = :expert');
        } else {
            $builder->where('t.candidate = :candidate');
        }

        $builder
            ->andWhere('t.updatedAt >= :from')
            ->andWhere('t.updatedAt <= :to')
            ->andWhere('t.status in (:statuses)')
        ;

        // display transaction with only 3 main statuses
        $parameters['statuses'] = Transaction::getStatuses();

        // if selected status is not default status - use it in filtering
        if ($parameters['status'] !== $defaultStatus) {
            $builder->andWhere('t.status = :status');
        } else {
            // otherwise we don't need to add default status in filtering
            // cause it will block fetching of all the results
            unset($parameters['status']);
        }

        $builder->orderBy('t.id', 'DESC');
        $builder->setParameters($parameters);

        return $builder->getQuery()->getResult();
    }
}