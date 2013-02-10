<?php

namespace FreeAgent\Bitter\UnitOfTime;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
interface UnitOfTimeInterface
{
    public function __construct($eventName, \DateTime $dateTime);
    public function getUnitOfTimeName();
    public function getDateTime();
    public function getDateTimeFormated();
    public function getKey();
}
