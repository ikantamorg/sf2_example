<?php

namespace Domain\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\CoreBundle\Entity\AdminSetting;

class AdminSettingsLoader implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $settings = [
            'payment.paypal_email' => ['Payment Paypal Email', 'rd.drgn@yahoo.com'],
            'payment.fee_percent' => ['Payment  Fee Percent', '5'],
            'payment.currency_code' => ['Payment Currency Code', 'USD'],
            'payment.memo' => ['Payment Page Memo', 'Some company memo displayed at payment page'],
            'payment.language' => ['Payment Page Language', 'en_US'],
            'payment.mode' => ['Payment Paypal Mode', 'sandbox'],
            'payment.acct1.UserName' => ['Payment Paypal Dev App Username', 'rd.drgn_api1.yahoo.com'],
            'payment.acct1.Password' => ['Payment Paypal Dev App Password', '1383294466'],
            'payment.acct1.Signature' => [
                'Payment Paypal Dev App Signature',
                'AFcWxV21C7fd0v3bYYYRCpSSRl31AgtFIejOzE9VibsEgwhsFvvMAeSd'
            ],
            'payment.acct1.AppId' => [
                'Payment Paypal Dev App AppId',
                'APP-80W284485P519543T'
            ],
            'site.preferences.maintenance' => ['Maintenance mode', 0]
        ];

        $repository = $manager->getRepository('CoreBundle:AdminSetting');

        foreach ($settings as $label => $params) {

            if ($repository->findOneBy(['label' => $label])) {
                continue;
            }

            $adminSetting = new AdminSetting;
            $adminSetting->setLabel($label);
            $adminSetting->setName($params[0]);
            $adminSetting->setValue($params[1]);
            $manager->persist($adminSetting);
        }

        $manager->flush();
    }
}
