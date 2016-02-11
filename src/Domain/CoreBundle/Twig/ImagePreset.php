<?php
/**
 * User: Dred
 * Date: 05.11.13
 * Time: 14:34
 */

namespace Domain\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFunction;
use Domain\CoreBundle\Entity\Image;

class ImagePreset extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Try to get preset url from image
     *
     * @param Image $image
     * @param $preset
     *
     * @return null|string
     */
    public function getPresetUrl(Image $image = null, $preset)
    {
        if (!$image) {
            return null;
        }

        $request = $this->container->get('request');

        return $request->getBasePath().$image->getPresetUrl($preset);
    }

    /**
     * {@inherited}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'image_preset',
                [
                    $this,
                    'getPresetUrl'
                ],
                [
                    'is_safe' => [
                        'html'
                    ]
                ]
            ),
        ];
    }

    /**
     * {@inherited}
     */
    public function getName()
    {
        return 'core_image_preset_url';
    }

}
