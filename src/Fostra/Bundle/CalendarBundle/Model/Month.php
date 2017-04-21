<?php
/**
 * Copyright (c) 2017 Fostral Ltd. All rights reserved.
 *
 * This file is part of Fostra systems and belong to Fostral Ltd. Distribution is subject to licence terms and conditions.
 *
 */

namespace Fostra\Bundle\CalendarBundle\Model;

use Fostra\Bundle\CalendarBundle\Model\Date as Day;
use Fostra\Bundle\CalendarBundle\Model\Week;

/**
 * Class Month
 * @package Fostra\Bundle\CalendarBundle\Model
 */
class Month implements \Iterator
{
    /** @var Day[] $days */
    public $days = [];

    /** @var Week[] */
    public $weeks = [];

    /** @var string $type */
    private $type = 'days';

    /** @var int $month */
    protected $month;

    /** @var int $year */
    protected $year;

    /**
     * Month constructor.
     * @param int|null $year
     * @param int|null $month
     */
    public function __construct(int $year = null, int $month = null)
    {
        $today = new Day();

        if (empty($year)) {
            $year = $today->getYear();
        }

        if (empty($month)) {
            $month = $today->getMonth();
        }

        $this->year = $year;
        $this->month = $month;

        $this->setDays();
    }

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function current()
    {
        return current($this->{$this->type});
    }


    public function next()
    {
        return next($this->{$this->type});
    }


    public function rewind()
    {
        return reset($this->{$this->type});
    }


    public function key()
    {
        return key($this->{$this->type});
    }


    public function valid()
    {
        $validKeys = [];

        for ($i = 1; $i <= $this->getLastDay()->getDay(); $i++) {
            $validKeys[$i] = $i;
        }

        $key = key($this->{$this->type});
        return !empty($key) && !is_bool($key) && in_array($key, $validKeys);
    }

    public function getMonth()
    {
        $this->setDays();
        $this->setWeeks();

        return $this->{$this->type};
    }

    public function setMonth(int $month)
    {
        $this->month = $month;

        return $this;
    }

    public function setWeeks()
    {
        foreach ($this->days as $d => $day) {
            $week = $day->getWeekNumber();
            $year = $day->getISOYear();
            $this->weeks[$week] = new Week($year, $week);
        }
    }

    public function markDays(array $days)
    {
        if ($this->type === 'weeks') {
            foreach ($this->weeks as $weekNo => $week) {
                /**
                 * @var int $weekDay
                 * @var Day $day
                 */
                foreach ($week->getWeek() as $weekDay => $day) {
                    if (in_array($day->getTimestamp(), $days)) $day->mark();
                }
            }
        }

        return $this;
    }

    public function setDays()
    {
        $day = new Day();
        $day->setDate($this->year, $this->month, 1);

        $d = 1;

        while ($day->getMonth() === $this->month) {
            $day = new Day();
            $day->setDate($this->year, $this->month, $d);
            $this->days[$d] = $day->copy();
            $d++;
        }

        $this->setWeeks();

        return $this;
    }

    /**
     * @return array
     */
    public function getDays()
    {
        return $this->days;
    }

    public function getWeeks()
    {
        return $this->weeks;
    }

    /**
     * @return Month
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * __clone days in month
     */
    public function __clone()
    {
        /**
         * @var int $d
         * @var Day $day
         */
        foreach ($this->days as $d => $day) {
            $this->days[$d] = clone $day->copy();
        }
        /**
         * @var int $w
         * @var Week $week
         */
        foreach ($this->weeks as $w => $week) {
            $this->weeks[$w] = clone $week->copy();
        }
    }

    /**
     * @return Day
     */
    public function getFirstDay()
    {
        /** @var Day $day */
        $day = reset($this->days);
        return $day;
    }

    /**
     * @return Day
     */
    public function getLastDay()
    {
        /** @var Day $day */
        $day = end($this->days);
        return $day;
    }

    /**
     * @return int|null
     */
    public function getMonthNumber()
    {
        return $this->month;
    }

    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param bool $long
     * @return string
     */
    public function getMonthName($long = false) {
        $format = 'M';

        if ($long) {
            $format = 'F';
        }

        return $this->getFirstDay()->format($format);
    }
}