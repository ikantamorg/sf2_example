<?php
/**
 * User: alkuk
 * Date: 14.02.14
 * Time: 1:10
 */

namespace Domain\CoreBundle\Util;

use Resque as OriginalResque;
use Domain\CoreBundle\Util\ResqueJob;
use Resque_Event;

class Resque extends OriginalResque
{

    public static function enqueue($queue, $class, $args = null, $trackStatus = false)
    {
        $result = ResqueJob::create($queue, $class, $args, $trackStatus);
        if ($result) {
            Resque_Event::trigger('afterEnqueue', array(
                'class' => $class,
                'args'  => $args,
                'queue' => $queue,
            ));
        }

        return $result;
    }

}
 