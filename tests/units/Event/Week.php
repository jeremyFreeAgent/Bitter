<?php

namespace Bitter\tests\units\Event;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use Bitter\Event\Week as TestedWeek;

class Week extends atoum\test
{
    public function testConstruct()
    {
        $week = new TestedWeek('drink_a_bitter_beer', new DateTime());

        $this
            ->object($week)
            ->isInstanceOf('Bitter\Event\AbstractEvent')
            ->isInstanceOf('Bitter\Event\EventInterface')
        ;
    }

    public function testGetDateTimeFormated()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $week = new TestedWeek('drink_a_bitter_beer', $dateTime);

        $this
            ->string($week->getDateTimeFormated())
            ->isEqualTo('2012-W45')
        ;
    }
}
