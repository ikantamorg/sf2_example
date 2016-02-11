<?php

namespace Domain\CoreBundle\Service\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer;

class MenuRendererTabsService extends ListRenderer
{
    public function render(ItemInterface $item, array $options = array())
    {

        $options = array_merge(
            [
                'currentClass' => 'activetab',
            ],
            $options
        );


        return parent::render($item, $options);
    }
}
