<?php
/**
 * User: dev
 * Date: 22.01.14
 * Time: 15:57
 */

namespace Domain\CoreBundle\Util;

use DateTime;

/**
 * Class DateRange
 */
class DateRange
{

    /**
     * @var DateTime
     */
    protected $start;

    /**
     * @var DateTime
     */
    protected $end;

    /**
     * @param DateTime $start
     * @param null | DateTime $end
     */
    public function __construct(DateTime $start, DateTime $end = null)
    {
        if (!$end) {
            $end = $start;
        }

        if ($end < $start) {
            $this->start = $end;
            $this->end = $start;
        } else {
            $this->start = $start;
            $this->end = $end;
        }
    }

    /**
     * Get start date of range
     *
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get end date of range
     *
     * @return DateTime|null
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Check whether the Date in DateRange
     *
     * @param DateTime $date
     *
     * @return bool
     */
    public function containsDate(DateTime $date)
    {
        return ($this->start <= $date && $this->end >= $date);
    }

    /**
     * Check whether the DateRange in DateRange
     *
     * @param DateRange $range
     *
     * @return bool
     */
    public function containsRange(DateRange $range)
    {
        return (
            $this->start <= $range->getStart() &&
            $this->end >= $range->getEnd()
        );
    }

    /**
     * TODO
     *
     * @param DateRange $range
     */
    public function crossRange(DateRange $range)
    {

    }

} 