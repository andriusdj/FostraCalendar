<?php
/**
 * Copyright (c) 2017 Fostral Ltd. All rights reserved.
 *
 * This file is part of Fostra systems and belong to Fostral Ltd. Distribution is subject to licence terms and conditions.
 *
 */

namespace Fostra\Bundle\CalendarBundle\Service;

use Fostra\Bundle\CalendarBundle\Model\Date as Day;
use Fostra\Bundle\CalendarBundle\Model\Month;
use Fostra\Bundle\CalendarBundle\Model\Week;
use Fostra\Bundle\CalendarBundle\Model\Year;

/**
 * Class myCalendar
 * @package Fostra\Bundle\CalendarBundle\Service
 */
class CalendarService
{
    /** @var Year $year */
    private $year;

    /** @var Year $previousYear */
    private $prevYear;

    /** @var Year $nextYear */
    private $nextYear;

    /** @var array $calendar */
    private $calendar = [];

    /** @var int $yearNumber */
    private $yearNumber;

    /** @var int $monthNumber */
    private $monthNumber;

    /** @var Day $today */
    public $today;

    public function __construct(int $year = null, int $month = null)
    {
        $this->today = new Day();

        if (empty($year)) {
            $year = $this->today->getYear();
        }

        if (empty($month)) {
            $month = $this->today->getMonth();
        }

        $this->yearNumber = $year;
        $this->monthNumber = $month;

    }

    public function getWeeks() {
        return $this->year->setType('weeks');
    }

    /**
     * @return Month
     */
    public function buildSingleMonth()
    {
        $month = new Month($this->yearNumber, $this->monthNumber);
        return $month->setType('weeks')->getMonth();
    }

    /**
     * @param array $calendar [year=>monthnumber]
     * @return array
     */
    public function buildMultipleMonths(array $calendar)
    {
        $result = [];

        foreach ($calendar as $year => $months) {
            foreach ($months as $month) {
                $monthObject = new Month($year, $month);
                $monthObject->setType('weeks')->setDays()->setWeeks();
                $result[$year][$month] = $monthObject;
            }
        }

        return $result;
    }

    /**
     * @param int $year
     * @return $this
     */
    public function setYear(int $year)
    {
        $this->year = new Year($year);
        $this->yearNumber = $year;

        return $this;
    }

    /**
     * @param array $days
     * @return array
     */
    public function highlightDays(array $days = []) {
        $months = [];
        $day = new Day();
        $timestamp = (int) reset($days);
        $day->setTimestamp($timestamp);
        $markedDays = [];

        foreach ($days as $ts) {
            $d = new Day();
            $markedDays[] = $d->setTimestamp($ts)->format('Ymd');
            $year = $day->setTimestamp($ts)->getYear();
            $month = $day->setTimestamp($ts)->getMonth();
            $months[$year][$month] = $month;
        }

        $calendar = $this->buildMultipleMonths($months);
        /**
         * @var int $year
         * @var Month[] $months
         */
        foreach ($calendar as $year => $months) {
            /**
             * @var int $monthNumber
             * @var Month $month
             */
            foreach ($months as $monthNumber => $month) {
                /**
                 * @var int $weekNumber
                 * @var Week $week
                 */
                foreach ($month->getWeeks() as $weekNumber => $week) {
                    /**
                     * @var int $weekDay
                     * @var Day $calendarDay
                     */
                    foreach ($week->getWeek() as $weekDay => $calendarDay) {
                        if (in_array($calendarDay->format('Ymd'), $markedDays)) $calendarDay->mark();
                        //echo $calendarDay->format('Ymd').'->' . $markedDays . "\n";
                    }
                }
            }
        }

        return $calendar;
    }

    /**
     * @param int|null $monthNumber
     * @param bool $long
     * @return string
     */
    public function getMonthName(int $monthNumber = null, bool $long = false)
    {
        if (empty($monthNumber)) {
            $monthNumber = $this->monthNumber;
        }

        $month = new Month($this->yearNumber, $this->monthNumber);

        return $month->getMonthName($long);
    }

    /**
     * @return int
     */
    public function getYearNumber()
    {
        return $this->yearNumber;
    }

    /**
     * @return int
     */
    public function getMonthNumber()
    {
        return $this->monthNumber;
    }
}