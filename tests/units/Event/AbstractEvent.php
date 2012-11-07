<?php

namespace Bitter\tests\units\Event;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use Bitter\Event\Day;
use Bitter\Event\AbstractEvent as TestedAbstractEvent;

class AbstractEvent extends atoum\test
{
    public function testConstruct()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $event = new \mock\Bitter\Event\AbstractEvent('drink_a_bitter_beer', $dateTime);

        $this
            ->variable($event->getDateTime())
            ->isIdenticalTo($dateTime)
        ;
        $this
            ->variable($event->getEventName())
            ->isIdenticalTo('drink_a_bitter_beer')
        ;
    }

    public function testGetPrefixKey()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $event = new \mock\Bitter\Event\AbstractEvent('drink_a_bitter_beer', $dateTime);

        $this
            ->string($event->getPrefixKey())
            ->isEqualTo('bitter')
        ;
    }

    public function testGetKey()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $event = new \mock\Bitter\Event\AbstractEvent('drink_a_bitter_beer', $dateTime);

        $event->getMockController()->getDateTimeFormated = '2012-11-06';

        $this
            ->string($event->getKey())
            ->isEqualTo('bitter_drink_a_bitter_beer_2012-11-06')
        ;
    }

    public function testGetDateTimeFormated()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $event = new \mock\Bitter\Event\AbstractEvent('drink_a_bitter_beer', $dateTime);

        $this
            ->exception(function() use ($event) { $event->getDateTimeFormated(); })
            ->isInstanceOf('\Exception')
            ->hasMessage("getDateTimeFormated method must be defined.")
        ;
    }
}
