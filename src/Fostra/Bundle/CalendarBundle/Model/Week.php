<?php
/**
 * Copyright (c) 2017 Fostral Ltd. All rights reserved.
 *
 * This file is part of Fostra systems and belong to Fostral Ltd. Distribution is subject to licence terms and conditions.
 *
 */

namespace Fostra\Bundle\CalendarBundle\Model;

use Fostra\Bundle\CalendarBundle\Model\Date as Day;

/**
 * Class Week
 * @package Fostra\Bundle\CalendarBundle\Model
 */
class Week Implements \Iterator
{
    /** @var Day[] $days */
    protected $days = [];

    /** @var  int $weekNumber */
    protected $week;

    /** @var  int $year */
    protected $year;

    /** @var  array $month */
    protected $month;

    /**
     * Week constructor.
     * @param int|null $year
     * @param int|null $week
     */
    public function __construct(int $year = null, int $week = null)
    {
        $today = new Day();

        if (empty($year)) {
            $year = $today->getYear();
        }

        if (empty($week)) {
            $week = $today->getWeekNumber();
        }

        $this->year = $year;

        $this->setWeek($week);
    }


    public function current()
    {
        return current($this->days);
    }


    public function next()
    {
        return next($this->days);
    }


    public function rewind()
    {
        return reset($this->days);
    }


    public function key()
    {
        return key($this->days);
    }


    public function valid()
    {
        $validKeys = [1,2,3,4,5,6,7];
        $key = key($this->days);
        return !empty($key) && !is_bool($key) && in_array($key, $validKeys);
    }

    /**
     * @param int $year
     * @return $this
     */
    public function setYear(int $year)
    {
        $this->year = $year;

        return $this;
    }
    /**
     * @param int $week
     * @return $this
     */
    public function setWeek(int $week)
    {
        $this->week = $week;

        for ($d = 1; $d <= 7; $d++){
            $day = new Day();
            $this->days[$d] = $day->setISODate($this->year, $week, $d);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getWeek()
    {
        return $this->days;
    }

    /**
     * @return Week
     */
    public function copy() {
        return clone $this;
    }

    /**
     * __clone days in week
     */
    public function __clone()
    {
        /**
         * @var int $weekDay
         * @var Day $day
         */
        foreach ($this->days as $weekDay => $day) {
            $this->days[$weekDay] = $day->copy();
        }
    }

    /**
     * @return array|int
     */
    public function getMonth()
    {
        /** @var Day $firstDay */
        $firstDay = reset($this->days);
        /** @var Day $lastDay */
        $lastDay = end($this->days);

        $firstDayMonth = $firstDay->getMonth();
        $lastDayMonth = $lastDay->getMonth();

        if ($firstDayMonth === $lastDayMonth) {
            return $firstDayMonth;
        }

        return [$firstDayMonth, $lastDayMonth];
    }
}