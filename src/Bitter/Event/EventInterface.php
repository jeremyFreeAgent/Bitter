<?php

namespace Bitter\Event;

interface EventInterface
{
    public function __construct($eventName, \DateTime $dateTime);
    public function getEventName();
    public function getDateTime();
    public function getDateTimeFormated();
    public function getPrefixKey();
    public function getKey();
}
