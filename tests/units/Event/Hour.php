<?php

namespace Bitter\tests\units\Event;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use Bitter\Event\Hour as TestedHour;

class Hour extends atoum\test
{
    public function testConstruct()
    {
        $hour = new TestedHour('drink_a_bitter_beer', new DateTime());

        $this
            ->object($hour)
            ->isInstanceOf('Bitter\Event\AbstractEvent')
            ->isInstanceOf('Bitter\Event\EventInterface')
        ;
    }

    public function testGetDateTimeFormated()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $hour = new TestedHour('drink_a_bitter_beer', $dateTime);

        $this
            ->string($hour->getDateTimeFormated())
            ->isEqualTo('2012-11-06-15')
        ;
    }
}
