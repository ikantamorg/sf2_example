<?php
/**
 * User: Dred
 * Date: 02.10.13
 * Time: 10:27
 */

namespace Domain\CoreBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Domain\CoreBundle\Form\DataTransformer\EntityToIdTransformer;

class HiddenEntityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new EntityToIdTransformer($this->om, $options['class']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'class' => null,
                'invalid_message' => 'The entity does not exist',
            ]
        );
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'hidden_entity';
    }

}
