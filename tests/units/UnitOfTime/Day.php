<?php

namespace FreeAgent\Bitter\tests\units\UnitOfTime;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use FreeAgent\Bitter\UnitOfTime\Day as TestedDay;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Day extends atoum\test
{
    public function testConstruct()
    {
        $day = new TestedDay('drink_a_bitter_beer', new DateTime());

        $this
            ->object($day)
            ->isInstanceOf('FreeAgent\Bitter\UnitOfTime\AbstractUnitOfTime')
            ->isInstanceOf('FreeAgent\Bitter\UnitOfTime\UnitOfTimeInterface')
        ;
    }

    public function testGetDateTimeFormated()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $day = new TestedDay('drink_a_bitter_beer', $dateTime);

        $this
            ->string($day->getDateTimeFormated())
            ->isEqualTo('2012-11-06')
        ;
    }
}
