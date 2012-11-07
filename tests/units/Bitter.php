<?php

namespace Bitter\tests\units;

require_once __DIR__ . '/../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use Bitter\Bitter as TestedBitter;
use Bitter\Event\Day;

class Bitter extends atoum\test
{
    public function testConstruct()
    {
        $redisClient = new \Predis\Client();

        $bitter = new TestedBitter($redisClient);

        $this
            ->variable($bitter->getRedisClient())
            ->isIdenticalTo($redisClient)
        ;
    }

    public function testMarkEvent()
    {
        $redisClient = new \Predis\Client();

        $redisClient->flushdb();

        $bitter = new TestedBitter($redisClient);

        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $day = new Day('drink_a_bitter_beer', $dateTime);

        $this
            ->variable($bitter->count($day))
            ->isIdenticalTo(0)
        ;
        $this
            ->boolean($bitter->contain($day, 13003))
            ->isFalse()
        ;

        $bitter->mark('drink_a_bitter_beer', 13003, $dateTime);

        $this
            ->variable($bitter->count($day))
            ->isIdenticalTo(1)
        ;
        $this
            ->boolean($bitter->contain($day, 13003))
            ->isTrue()
        ;

        $bitter->mark('drink_a_bitter_beer', 13003, $dateTime);

        $this
            ->variable($bitter->count($day))
            ->isIdenticalTo(1)
        ;
        $this
            ->boolean($bitter->contain($day, 13003))
            ->isTrue()
        ;

        $dateTime = new DateTime();
        $day = new Day('drink_a_bitter_beer', $dateTime);
        $this
            ->boolean($bitter->contain($day, 13))
            ->isFalse()
        ;
        $bitter->mark('drink_a_bitter_beer', 13);
        $this
            ->boolean($bitter->contain($day, 13))
            ->isTrue()
        ;
    }
}
