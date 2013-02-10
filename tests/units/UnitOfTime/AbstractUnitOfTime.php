<?php

namespace FreeAgent\Bitter\tests\units\UnitOfTime;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use FreeAgent\Bitter\UnitOfTime\AbstractUnitOfTime as TestedAbstractUnitOfTime;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class AbstractUnitOfTime extends atoum\test
{
    public function testConstruct()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $event = new \mock\FreeAgent\Bitter\UnitOfTime\AbstractUnitOfTime('drink_a_bitter_beer', $dateTime);

        $this
            ->variable($event->getDateTime())
            ->isIdenticalTo($dateTime)
        ;
        $this
            ->variable($event->getUnitOfTimeName())
            ->isIdenticalTo('drink_a_bitter_beer')
        ;
    }

    public function testGetKey()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $event = new \mock\FreeAgent\Bitter\UnitOfTime\AbstractUnitOfTime('drink_a_bitter_beer', $dateTime);

        $event->getMockController()->getDateTimeFormated = '2012-11-06';

        $this
            ->string($event->getKey())
            ->isEqualTo('drink_a_bitter_beer:2012-11-06')
        ;
    }
}
