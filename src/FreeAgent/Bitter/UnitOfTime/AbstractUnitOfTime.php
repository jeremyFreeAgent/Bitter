<?php

namespace FreeAgent\Bitter\UnitOfTime;

use \DateTime;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
abstract class AbstractUnitOfTime
{
    protected $eventName;
    protected $dateTime;

    public function __construct($eventName, DateTime $dateTime = null)
    {
        $this->eventName = $eventName;
        $this->dateTime  = is_null($dateTime) ? new DateTime : $dateTime;
    }

    public function getUnitOfTimeName()
    {
        return $this->eventName;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }

    abstract public function getDateTimeFormated();

    public function getKey()
    {
        return sprintf('%s:%s', $this->getUnitOfTimeName(), $this->getDateTimeFormated());
    }
}
