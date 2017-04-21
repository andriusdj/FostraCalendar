<?php
/**
 * Copyright (c) 2017 Fostral Ltd. All rights reserved.
 *
 * This file is part of Fostra systems and belong to Fostral Ltd. Distribution is subject to licence terms and conditions.
 *
 */

/**
 * Created by PhpStorm.
 * User: andrius
 * Date: 17.4.18
 * Time: 21.20
 */
namespace Tests\Unit\Fostra\Bundle\CalendarBundle;

use Fostra\Bundle\CalendarBundle\Model\Date;
use PHPUnit\Framework\TestCase;

class DateModelTest extends TestCase
{
    public function testIsSameDay()
    {
        $today = new Date();
        $testDay = new Date();

        $this->assertTrue($testDay->isSameDay($today), 'Same Day');

        $testDay->modify('+1 day');
        $this->assertFalse($testDay->isSameDay($today), 'Not Same Day');
    }

    public function testMarking()
    {
        $date = new Date();

        $this->assertFalse($date->isMarked(), 'This is not marked at all');
        $this->assertFalse($date->isMarked(23), 'This is not marked at all and 23');
        $this->assertFalse($date->getMark(), 'The mark is False by default');

        $date->mark(5);

        $this->assertTrue($date->isMarked(), 'This is marked');
        $this->assertTrue($date->isMarked(5, true), 'This is marked int 5');
        $this->assertTrue($date->isMarked('5', false), 'This is marked like 5');
        $this->assertTrue(5 === $date->getMark(), 'The mark is 5');

        $this->assertFalse($date->isMarked('5', true), 'This is not marked str 5');
        $this->assertFalse($date->isMarked(3), 'This is not marked 3');
    }

    public function testCopiesAndOther()
    {
        $today = new Date();
        $sameday = new Date();

        $this->assertEquals($today, $sameday, 'Dates are equals');
        $this->assertTrue($today->isEqual($sameday), 'Dates are same');

        $yesterday = $today->prev();
        $this->assertNotSame($yesterday, $today);
        $this->assertTrue($today->isEqual($sameday), 'sameday is still today');
        $this->assertFalse($today->isEqual($yesterday), 'today is not yesterday');

        $todayAgain = $yesterday->next();
        $this->assertTrue($today->isEqual($todayAgain), 'today is todayAgain');
        $this->assertFalse($today->isEqual($yesterday), 'yesterday is still yesterday and not today');

        $tomorrow = $todayAgain->next();
        $this->assertFalse($today->isEqual($tomorrow), 'tomorrow is not today');
        $this->assertTrue($today->isEqual($todayAgain), 'todayAgain is still todayAgain and equal today');
    }

}