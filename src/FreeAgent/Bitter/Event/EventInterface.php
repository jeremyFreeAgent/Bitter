<?php

namespace FreeAgent\Bitter\Event;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
interface EventInterface
{
    public function __construct($eventName, \DateTime $dateTime);
    public function getEventName();
    public function getDateTime();
    public function getDateTimeFormated();
    public function getKey();
}
