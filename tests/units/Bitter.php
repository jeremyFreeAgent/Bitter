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
            ->boolean($bitter->in(404, $day))
            ->isFalse()
        ;

        $bitter->mark('drink_a_bitter_beer', 404, $dateTime);

        $this
            ->variable($bitter->count($day))
            ->isIdenticalTo(1)
        ;
        $this
            ->boolean($bitter->in(404, $day))
            ->isTrue()
        ;

        $bitter->mark('drink_a_bitter_beer', 404, $dateTime);

        $this
            ->variable($bitter->count($day))
            ->isIdenticalTo(1)
        ;
        $this
            ->boolean($bitter->in(404, $day))
            ->isTrue()
        ;

        $redisClient->flushdb();

        $day = new Day('drink_a_bitter_beer', new DateTime());
        $this
            ->boolean($bitter->in(13, $day))
            ->isFalse()
        ;
        $bitter->mark('drink_a_bitter_beer', 13);
        $this
            ->boolean($bitter->in(13, $day))
            ->isTrue()
        ;
    }

    public function testbitOpAnd()
    {
        $redisClient = new \Predis\Client();

        $redisClient->flushdb();

        $bitter = new TestedBitter($redisClient);

        $yesterday = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today     = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->boolean($bitter->bitOpAnd('test', $today, $yesterday)->in(13, 'test'))
            ->isTrue()
        ;

        $this
            ->boolean($bitter->bitOpAnd('test', $today, $yesterday)->in(404, 'test'))
            ->isFalse()
        ;
    }
}
