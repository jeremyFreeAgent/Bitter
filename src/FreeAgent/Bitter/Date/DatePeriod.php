<?php

namespace FreeAgent\Bitter\Date;

use \DateTime;
use \DateInterval;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class DatePeriod extends \DatePeriod
{
    const CREATE_FROM = 'from';
    const CREATE_TO   = 'to';

    public function toArray($dateToString = false)
    {
        $dates = array();
        foreach ($this as $date) {
            $dates[] = true === $dateToString ? $date->format('Y-m-d H:i:s') : $date;
        }

        return $dates;
    }

    public static function createForHour(DateTime $from, DateTime $to, $fromOrTo = self::CREATE_FROM)
    {
        if ($from->format('Y-m-d') != $to->format('Y-m-d')) {
            if (self::CREATE_TO !== $fromOrTo) {
                $from->setTime($from->format('H'), 0, 0);
                $to = clone($from);
                $to->setTime(24, 0, 0);
            } else {
                $from = clone($to);
                $from->setTime(0, 0, 0);
                $to->setTime($to->format('H'), 0, 0);
            }
        } else {
            $from->setTime($from->format('H'), 0, 0);
            $to->setTime($to->format('H'), 0, 0);
        }

        return new DatePeriod($from, new DateInterval('PT1H'), $to);
    }

    public static function createForDay(DateTime $from, DateTime $to, $fromOrTo = self::CREATE_FROM)
    {
        $mFrom = $from;
        $mTo = $to;
        if ($mFrom->format('Y-m') != $mTo->format('Y-m')) {
            if (self::CREATE_TO !== $fromOrTo) {
                $mFrom->setTime(0, 0, 0);
                $mFrom->setDate($mFrom->format('Y'), $mFrom->format('m'), $mFrom->format('d'));
                $mTo = clone($mFrom);
                $mTo->setDate($mFrom->format('Y'), $mFrom->format('m') + 1, 1);
            } else {
                $mTo->setTime(0, 0, 0);
                $mFrom = clone($mTo);
                $mFrom->setDate($mFrom->format('Y'), $mFrom->format('m'), 1);
                $mTo->setDate($mTo->format('Y'), $mTo->format('m'), $mTo->format('d'));
            }
        } else {
            $mFrom->setTime(0, 0, 0);
            $mTo->setTime(0, 0, 0);
        }

        return new DatePeriod($mFrom, new DateInterval('P1D'), $mTo, self::CREATE_TO !== $fromOrTo || $from->format('Y-m') == $to->format('Y-m') ? self::EXCLUDE_START_DATE : null);
    }

    public static function createForMonth(DateTime $from, DateTime $to, $fromOrTo = self::CREATE_FROM)
    {
        $mFrom = $from;
        $mTo = $to;
        if ($mFrom->format('Y') != $to->format('Y')) {
            if (self::CREATE_TO !== $fromOrTo) {
                $mFrom->setTime(0, 0, 0);
                $mFrom->setDate($mFrom->format('Y'), $mFrom->format('m'), 1);
                $mTo = clone($mFrom);
                $mTo->setDate($mFrom->format('Y'), 13, 1);
            } else {
                $mTo->setTime(0, 0, 0);
                $mFrom = clone($mTo);
                $mFrom->setDate($mFrom->format('Y'), 1, 1);
                $mTo->setDate($mTo->format('Y'), $mTo->format('m'), 1);
            }
        } else {
            $mFrom->setDate($mFrom->format('Y'), $mFrom->format('m'), 1);
            $mFrom->setTime(0, 0, 0);
            $mTo->setDate($mTo->format('Y'), $mTo->format('m'), 1);
            $mTo->setTime(0, 0, 0);
        }

        return new DatePeriod($mFrom, new DateInterval('P1M'), $mTo, self::CREATE_TO !== $fromOrTo || $from->format('Y') == $to->format('Y') ? self::EXCLUDE_START_DATE : null);
    }

    public static function createForYear(DateTime $from, DateTime $to)
    {
        $from->setDate($from->format('Y'), 1, 1);
        $from->setTime(0, 0, 0);
        $to->setDate($to->format('Y'), 1, 1);
        $to->setTime(0, 0, 0);

        return new DatePeriod($from, new DateInterval('P1Y'), $to, self::EXCLUDE_START_DATE);
    }
}
