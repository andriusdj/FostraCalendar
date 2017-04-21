<?php
/**
 * Copyright (c) 2017 Fostral Ltd. All rights reserved.
 *
 * This file is part of Fostra systems and belong to Fostral Ltd. Distribution is subject to licence terms and conditions.
 *
 */


namespace Fostra\Bundle\CalendarBundle\Controller;

use Fostra\Bundle\CalendarBundle\Model\Date as Day;
use Fostra\Bundle\CalendarBundle\Model\Week;
use Fostra\Bundle\CalendarBundle\Model\Month;
use Fostra\Bundle\CalendarBundle\Service\CalendarService as Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CalendarController
 * @package Fostra\Bundle\CalendarBundle\Controller
 */
class CalendarController extends Controller
{
    /** @var Day $today */
    private $today;

    public function showCalendarAction($year = null, $month = null)
    {
        $this->today = new Day();

        $calendar = new Calendar($year, $month);

        $singleMonth = $calendar->buildSingleMonth();

        $responseHTML = $this->calendarHeader($calendar->getMonthName(), $calendar->getYearNumber());
        /**
         * @var int $weekNumber
         * @var Week $week
         */
        foreach ($singleMonth as $weekNumber => $week) {
            $responseHTML .= '<tr class="week-'.$weekNumber.'">';
            $responseHTML .= '<th class="week-number">' . $weekNumber . '</th>';
            /**
             * @var int $weekDay
             * @var Day $day
             */
            foreach ($week->getWeek() as $weekDay => $day) {
                $class = 'weekday-'.$day->getDayOfWeek();
                if ($day->isSameDay($this->today)) $class .= ' today';
                if ($day->getMonth() > $calendar->getMonthNumber()) $class .= ' next-month';
                if ($day->getMonth() < $calendar->getMonthNumber()) $class .= ' previous-month';
                $responseHTML .= '<td class="'.$class.'">' . $day->getDay() . '</td>';
            }
            $responseHTML .= '</tr>';
        }
        $responseHTML .= '</tbody></table>';

        return $responseHTML;
    }

    public function showMarkedCalendarAction(Request $request) {

        /** @var array $days */
        $days = $request->get('days');

        if (!is_array($days)) {
            $days = explode(',', $days);
        }

        $calendar = new Calendar();

        $calendarArray = $calendar->highlightDays($days);

        $responseHTML = '';

        foreach ($calendarArray as $year => $months) {
            /**
             * @var int $monthNumner
             * @var Month $month
             */
            foreach ($months as $monthNumner => $month) {
                $responseHTML .= $this->calendarHeader($month->getMonthName(true), $year);
                /**
                 * @var int $weekNumber
                 * @var Week $week
                 */
                foreach ($month->getWeeks() as $weekNumber => $week) {
                    $responseHTML .= '<tr class="week-'.$weekNumber.'">';
                    $responseHTML .= '<th class="week-number">' . $weekNumber . '</th>';
                    /**
                     * @var int $weekDay
                     * @var Day $day
                     */
                    foreach ($week->getWeek() as $weekDay => $day) {
                        $class = 'weekday-'.$day->getDayOfWeek();
                        if ($day->isSame($this->today)) $class .= ' today';
                        if ($day->getMonth() > $month->getMonthNumber()) $class .= ' next-month';
                        if ($day->getMonth() < $month->getMonthNumber()) $class .= ' previous-month';
                        if ($day->isMarked()) $class .= ' marked';
                        $responseHTML .= '<td class="'.$class.'">'.$day->getDay().'</td>';
                    }
                    $responseHTML .= '</tr>';
                }
                $responseHTML .= '</tbody></table>';
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([$responseHTML]);
        }

        return new Response($responseHTML);
    }

    public function calendarHeader($month, $year) {
        $monthCaption = $month . ', ' . $year;
        return
            '<table class="myCalendar">'.
            '<caption><i title="Previous Month" class="fa fa-arrow-left floatleft prevmonth_btn"></i><i title="Next Month" class="nextmonth_btn fa fa-arrow-right floatright"></i>'.$monthCaption.'</caption>'.
            '<thead><tr><th class="week-no"></th><th class="mon"></th><th class="tue"></th><th class="wed"></th><th class="thu"></th><th class="fri"></th><th class="sat"></th><th class="sun"></th></tr></thead>'.
            '<tbody>';
    }

}