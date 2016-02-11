<?php
/**
 * User: dev
 * Date: 04.10.13
 * Time: 23:46
 */

namespace Domain\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class NotificationsOptionsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $hour = 60*60;

        $builder
            ->add(
                'remindMeInterval',
                'choice',
                [
                    'label' => 'Remind me in',
                    'required' => false,
                    'choices' => [
                        1*$hour => '1 hour',
                        3*$hour => '3 hours',
                        6*$hour => '6 hours',
                        12*$hour => '12 hours',
                        24*$hour => '24 hours',
                    ],
                    'empty_value' => false,
                ]
            )
            ->add(
                'remindMeEmail',
                'checkbox',
                [
                    'label' => 'Email',
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Domain\CoreBundle\Entity\NotificationOptions',
                'validation_groups' => 'edit',
                'cascade_validation' => true,
                'csrf_protection'   => true
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'notifications_options_edit';
    }
}
