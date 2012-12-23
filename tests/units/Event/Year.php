<?php

namespace FreeAgent\Bitter\tests\units\Event;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use FreeAgent\Bitter\Event\Year as TestedYear;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Year extends atoum\test
{
    public function testConstruct()
    {
        $month = new TestedYear('drink_a_bitter_beer', new DateTime());

        $this
            ->object($month)
            ->isInstanceOf('FreeAgent\Bitter\Event\AbstractEvent')
            ->isInstanceOf('FreeAgent\Bitter\Event\EventInterface')
        ;
    }

    public function testGetDateTimeFormated()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $month = new TestedYear('drink_a_bitter_beer', $dateTime);

        $this
            ->string($month->getDateTimeFormated())
            ->isEqualTo('2012')
        ;
    }
}
