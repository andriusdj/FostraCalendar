<?php

namespace Fostra\Bundle\CalendarBundle\Model;

use DateTimeZone;
use Fostra\Bundle\CalendarBundle\Model\Month;
use Fostra\Bundle\CalendarBundle\Model\Date as Day;
use Fostra\Bundle\CalendarBundle\Model\Week;

/**
 * Class Year
 * @package Fostra\Bundle\CalendarBundle\Model
 */
class Year implements \RecursiveIterator
{
    /** @var  int $year */
    protected $year;

    /** @var Month[] $months */
    protected $months = [];

    /** @var Week[] $weeks */
    protected $weeks = [];

    /** @var string $type */
    private $type = 'months';
    /**
     * Year constructor.
     * @param int|null $year
     */
    public function __construct(int $year = null)
    {
        if (empty($year)) {
            $today = new Day();
            $year = $today->getYear();
        }

        $this->setYear($year);
        $this->setMonths();
        $this->setWeeks();
    }

    public function getChildren()
    {
        return $this->current();
    }

    public function hasChildren()
    {
        return $this->current() instanceof \Iterator;
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
        $validKeys = [
            'months' => function() { $r = []; for ($i = 1; $i <= 12; $i++) $r[$i] = $i; return $r; },
            'weeks' => function($_this) { $r = []; for ($i = 1; $i <= 53; $i++) $r[$i] = $i; return $r; }
        ];

        $key = key($this->{$this->type});
        return !empty($key) && !is_bool($key) && in_array($key, $validKeys);
    }

    public function setType(string $type) {
        $this->type = $type;

        return $this;
    }

    public function setYear(int $year)
    {
        $this->year = $year;

        return $this;
    }

    public function setMonths()
    {
        for ($m = 1; $m <= 12; $m++) {
            $this->months[$m] = new Month($this->year, $m);
        }

        return $this;
    }

    public function setWeeks()
    {
        for ($w = 1; $w <= 53; $w++) {
            if ($w === 53 && !date('L', mktime(null,null,null,1,1,$this->year))) continue;
            $this->weeks[$w] = new Week($this->year, $w);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getYear()
    {
        return $this->{$this->type};
    }

    /**
     * @return int
     */
    public function getYearNumber()
    {
        return $this->year;
    }

    /**
     * @return Year
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     *
     */
    public function __clone()
    {
        /**
         * @var int $m
         * @var Month $month
         */
        foreach ($this->months as $monthNumber => $month) {
            $this->months[$monthNumber] = $month->copy();
        }
    }
}