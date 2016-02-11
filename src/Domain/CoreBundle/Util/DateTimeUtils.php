<?php
/**
 * User: dev
 * Date: 02.12.13
 * Time: 13:06
 */

namespace Domain\CoreBundle\Util;

use DateTime;

/**
 * Class DateTimeUtils
 */
class DateTimeUtils
{

    /**
     * Check is time ranges cross
     *
     * @param DateTime $time1Start
     * @param DateTime $time1End
     * @param DateTime $time2Start
     * @param DateTime $time2End
     *
     * @return bool
     */
    static public function isTimeCross(
        DateTime $time1Start,
        DateTime $time1End,
        DateTime $time2Start,
        DateTime $time2End
    )
    {
        if (($time1Start <= $time2Start && $time1End > $time2Start)
            || ($time1Start < $time2End && $time1End >= $time2End)
        ) {
            return true;
        }

        return false;
    }

} 