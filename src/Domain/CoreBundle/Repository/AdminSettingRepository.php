<?php

namespace Domain\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AdminSettingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdminSettingRepository extends EntityRepository
{
    /**
     * Get query builder Settings by label
     *
     * @param $label
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQBSettingsByLabel($label)
    {
        $qb = $this->createQueryBuilder('setting');
        $qb->where(
            $qb->expr()->like('setting.label', ':label')
        )
            ->setParameter('label', $label);

        return $qb;
    }

    /**
     * Get query builder for payment settings
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPaymentQB()
    {
        return $this->getQBSettingsByLabel('payment.%');
    }

    /**
     * Get query builder for site preferences
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSitePreferencesQB()
    {
        return $this->getQBSettingsByLabel('site.preferences.%');
    }

    /**
     * Get all admin settings for payment
     * 
     * @return array
     */
    public function findPaymentSettings()
    {
        return $this->getPaymentQB()->getQuery()->getResult();
    }

    /**
     * Get all site preferences
     *
     * @return array
     */
    public function findSitePreferences()
    {
        return $this->getSitePreferencesQB()->getQuery()->getResult();
    }

    /**
     * Check is site in maintenance mode
     *
     * @return bool
     */
    public function isMaintenanceMode()
    {
        $maintenanceOptionName = 'site.preferences.maintenance';
        $option = $this->findOneBy(['label' => $maintenanceOptionName]);
        if (!$option) {
            return false;
        }

        return !!intval($option->getValue());
    }
}
