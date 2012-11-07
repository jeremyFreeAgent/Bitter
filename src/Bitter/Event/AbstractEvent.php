<?php

namespace Bitter\Event;

use \DateTime;
use \Exception;

abstract class AbstractEvent
{
    protected $prefixKey = 'bitter';
    protected $eventName;
    protected $dateTime;

    public function __construct($eventName, DateTime $dateTime = null)
    {
        $this->eventName = $eventName;
        $this->dateTime  = is_null($dateTime) ? new DateTime : $dateTime;
    }

    public function getPrefixKey()
    {
        return $this->prefixKey;
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
    /*{
        throw new Exception("getDateTimeFormated method must be defined.");
    }*/

    public function getKey()
    {
        return sprintf('%s_%s_%s', $this->getPrefixKey(), $this->getEventName(), $this->getDateTimeFormated());
    }
}
