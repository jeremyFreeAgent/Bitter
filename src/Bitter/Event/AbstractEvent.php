<?php

namespace Bitter\Event;

use \DateTime;
use \Exception;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
abstract class AbstractEvent
{
    protected $eventName;
    protected $dateTime;

    public function __construct($eventName, DateTime $dateTime = null)
    {
        $this->eventName = $eventName;
        $this->dateTime  = is_null($dateTime) ? new DateTime : $dateTime;
    }

    public function getEventName()
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
        return sprintf('%s_%s', $this->getEventName(), $this->getDateTimeFormated());
    }
}
