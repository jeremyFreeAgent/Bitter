<?php

namespace FreeAgent\Bitter\tests\units\Date;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use \DateInterval;
use FreeAgent\Bitter\Date\DatePeriod as TestedDatePeriod;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class DatePeriod extends atoum\test
{
    public function testToArray()
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-20 15:30:45');

        $datePeriodArray = array(
            '2012-11-14 15:30:45',
            '2012-11-15 15:30:45',
            '2012-11-16 15:30:45',
            '2012-11-17 15:30:45',
            '2012-11-18 15:30:45',
            '2012-11-19 15:30:45',
        );

        $this
            ->if($datePeriod = new TestedDatePeriod($from, new DateInterval('P1D'), $to, TestedDatePeriod::EXCLUDE_START_DATE))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;
    }

    public function testCreateForHour()
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 10:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 15:30:45');

        $datePeriodFromArray = $datePeriodToArray = array(
            '2012-11-15 10:00:00',
            '2012-11-15 11:00:00',
            '2012-11-15 12:00:00',
            '2012-11-15 13:00:00',
            '2012-11-15 14:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForHour($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForHour($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 10:30:45');

        $datePeriodFromArray = array(
            '2012-11-13 15:00:00',
            '2012-11-13 16:00:00',
            '2012-11-13 17:00:00',
            '2012-11-13 18:00:00',
            '2012-11-13 19:00:00',
            '2012-11-13 20:00:00',
            '2012-11-13 21:00:00',
            '2012-11-13 22:00:00',
            '2012-11-13 23:00:00',
        );

        $datePeriodToArray = array(
            '2012-11-15 00:00:00',
            '2012-11-15 01:00:00',
            '2012-11-15 02:00:00',
            '2012-11-15 03:00:00',
            '2012-11-15 04:00:00',
            '2012-11-15 05:00:00',
            '2012-11-15 06:00:00',
            '2012-11-15 07:00:00',
            '2012-11-15 08:00:00',
            '2012-11-15 09:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForHour($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForHour($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;
    }

    public function testCreateForDay()
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 10:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 15:30:45');

        $datePeriodFromArray = $datePeriodToArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForDay($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForDay($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 10:30:45');

        $datePeriodFromArray = $datePeriodToArray = array(
            '2012-11-11 00:00:00',
            '2012-11-12 00:00:00',
            '2012-11-13 00:00:00',
            '2012-11-14 00:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForDay($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForDay($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-08-20 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');

        $datePeriodFromArray = array(
            '2012-08-21 00:00:00',
            '2012-08-22 00:00:00',
            '2012-08-23 00:00:00',
            '2012-08-24 00:00:00',
            '2012-08-25 00:00:00',
            '2012-08-26 00:00:00',
            '2012-08-27 00:00:00',
            '2012-08-28 00:00:00',
            '2012-08-29 00:00:00',
            '2012-08-30 00:00:00',
            '2012-08-31 00:00:00',
        );

        $datePeriodToArray = array(
            '2012-11-01 00:00:00',
            '2012-11-02 00:00:00',
            '2012-11-03 00:00:00',
            '2012-11-04 00:00:00',
            '2012-11-05 00:00:00',
            '2012-11-06 00:00:00',
            '2012-11-07 00:00:00',
            '2012-11-08 00:00:00',
            '2012-11-09 00:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForDay($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForDay($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;
    }

    public function testCreateForMonth()
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 10:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 15:30:45');

        $datePeriodFromArray = $datePeriodToArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 15:30:45');

        $datePeriodFromArray = $datePeriodToArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-10-10 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 10:30:45');

        $datePeriodFromArray = $datePeriodToArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-09-10 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 10:30:45');

        $datePeriodFromArray = $datePeriodToArray = array(
            '2012-10-01 00:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-08-20 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');

        $datePeriodFromArray = $datePeriodToArray = array(
            '2012-09-01 00:00:00',
            '2012-10-01 00:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2010-08-20 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');

        $datePeriodFromArray = array(
            '2010-09-01 00:00:00',
            '2010-10-01 00:00:00',
            '2010-11-01 00:00:00',
            '2010-12-01 00:00:00',
        );

        $datePeriodToArray = array(
            '2012-01-01 00:00:00',
            '2012-02-01 00:00:00',
            '2012-03-01 00:00:00',
            '2012-04-01 00:00:00',
            '2012-05-01 00:00:00',
            '2012-06-01 00:00:00',
            '2012-07-01 00:00:00',
            '2012-08-01 00:00:00',
            '2012-09-01 00:00:00',
            '2012-10-01 00:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_FROM))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodFromArray)
            ->if($datePeriod = TestedDatePeriod::createForMonth($from, $to, TestedDatePeriod::CREATE_TO))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodToArray)
        ;
    }

    public function testCreateForYear()
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 10:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 15:30:45');

        $datePeriodArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-13 15:30:45');

        $datePeriodArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-10-10 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 10:30:45');

        $datePeriodArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-09-10 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-15 10:30:45');

        $datePeriodArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-08-20 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');

        $datePeriodArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2011-08-20 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');

        $datePeriodArray = array();

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2010-08-20 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');

        $datePeriodArray = array(
            '2011-01-01 00:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;

        $from = DateTime::createFromFormat('Y-m-d H:i:s', '2009-08-20 15:30:45');
        $to   = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-10 10:30:45');

        $datePeriodArray = array(
            '2010-01-01 00:00:00',
            '2011-01-01 00:00:00',
        );

        $this
            ->if($datePeriod = TestedDatePeriod::createForYear($from, $to))
            ->array($datePeriod->toArray(true))
            ->isEqualTo($datePeriodArray)
        ;
    }
}
