<?php

namespace FreeAgent\Bitter\tests\units\UnitOfTime;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use FreeAgent\Bitter\UnitOfTime\Month as TestedMonth;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Month extends atoum\test
{
    public function testConstruct()
    {
        $month = new TestedMonth('drink_a_bitter_beer', new DateTime());

        $this
            ->object($month)
            ->isInstanceOf('FreeAgent\Bitter\UnitOfTime\AbstractUnitOfTime')
            ->isInstanceOf('FreeAgent\Bitter\UnitOfTime\UnitOfTimeInterface')
        ;
    }

    public function testGetDateTimeFormated()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $month = new TestedMonth('drink_a_bitter_beer', $dateTime);

        $this
            ->string($month->getDateTimeFormated())
            ->isEqualTo('2012-11')
        ;
    }
}
