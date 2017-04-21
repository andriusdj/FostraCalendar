<?php
/**
 * Copyright (c) 2017 Fostral Ltd. All rights reserved.
 *
 * This file is part of Fostra systems and belong to Fostral Ltd. Distribution is subject to licence terms and conditions.
 *
 */

namespace Fostra\Bundle\CalendarBundle\Model;

/**
 * Class Date
 * @package Fostra\Bundle\CalendarBundle\Model
 */
class Date extends \DateTime
{
    /**
     * one day in seconds
     */
    const ONE_DAY = 86400;

    /** @var bool|int $marked */
    private $marked = false;

    /**
     * @param bool|int $mark
     * @return $this
     */
    public function mark($mark = true)
    {
        $this->marked = $mark;

        return $this;
    }

    /**
     * @param bool|int|null $mark
     * @param bool $strict
     * @return bool|int
     */
    public function isMarked($mark = null, bool $strict = false)
    {
        if (!empty($mark) && $strict) {

            $result = $this->marked === $mark;

        } elseif (!empty($mark) && !$strict) {

            $result = $this->marked == $mark;
        } else {

            $result = (bool) $this->marked;
        }

        return $result;
    }

    /**
     * @return bool|int
     */
    public function getMark()
    {
        return $this->marked;
    }

    /**
     * @return Date
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * @return int
     */
    public function getDay()
    {
        return (int) date('j', $this->getTimestamp());
    }

    /**
     * @return int
     */
    public function getDayOfWeek()
    {
        return (int) date('N', $this->getTimestamp());
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getWeekNumber()
    {
        $weekNumber = date('W', $this->getTimestamp());
        if (false === $weekNumber) {
            throw new \Exception($this->getTimestamp());
        }
        return (int) $weekNumber;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return (int) date('Y', $this->getTimestamp());
    }

    public function getISOYear()
    {
        return (int) date('o', $this->getTimestamp());
    }

    public function isLeapYear()
    {
        return (bool) date('L', $this->getTimestamp());
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return (int) date('n', $this->getTimestamp());
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function isEqual(\DateTime $date)
    {
        return $this->getTimestamp() === $date->getTimestamp();
    }

    /**
     * @param int $timestamp
     * @return bool
     */
    public function isSameDay(Date $date)
    {
        return $date->format('Ymd') === $this->format('Ymd');
    }

    /**
     * @return Date
     */
    public function prev()
    {
        $prevDay = $this->copy();
        $prevDay->setTimestamp($this->getTimestamp() - self::ONE_DAY);

        return $prevDay;
    }

    /**
     * @return Date
     */
    public function next()
    {
        $nextDay = $this->copy();
        $nextDay->setTimestamp($this->getTimestamp() + self::ONE_DAY);

        return $nextDay;
    }
}
