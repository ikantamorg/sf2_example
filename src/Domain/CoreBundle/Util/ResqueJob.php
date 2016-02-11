<?php
/**
 * User: alkuk
 * Date: 14.02.14
 * Time: 1:12
 */

namespace Domain\CoreBundle\Util;

use Resque_Job;
use Resque_Job_Status;
use InvalidArgumentException;
use Domain\CoreBundle\Util\Resque;

class ResqueJob extends Resque_Job
{
    /**
     * {inherited}
     */
    public static function create($queue, $class, $args = null, $monitor = false)
    {
        if($args !== null && !is_array($args)) {
            throw new InvalidArgumentException(
                'Supplied $args must be an array.'
            );
        }
        $id = md5(uniqid('', true));
        Resque::push($queue, array(
            'class'	=> $class,
            'args'	=> $args,
            'id'	=> $id,
        ));

        if($monitor) {
            Resque_Job_Status::create($id);
        }

        return $id;
    }

}
 