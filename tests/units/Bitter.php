<?php

namespace Bitter\tests\units;

require_once __DIR__ . '/../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use Bitter\Bitter as TestedBitter;
use Bitter\Event\Day;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Bitter extends atoum\test
{
    private function getRedisClient()
    {
        return new \Predis\Client();
    }

    public function testConstruct()
    {
        $redisClient = $this->getRedisClient();

        $bitter = new TestedBitter($redisClient);

        $this
            ->variable($bitter->getRedisClient())
            ->isIdenticalTo($redisClient)
        ;
    }

    public function testMarkEvent()
    {
        $redisClient = $this->getRedisClient();

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

        // Adding it a second time with the same dateTime !
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
        $redisClient = $this->getRedisClient();

        $redisClient->flushdb();

        $bitter = new TestedBitter($redisClient);

        $yesterday = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today     = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->variable($bitter->bitOpAnd('test_a', $today, $yesterday))
            ->isIdenticalTo($bitter)
        ;

        $this
            ->boolean($bitter->bitOpAnd('test_b', $today, $yesterday)->in(13, 'test_b'))
            ->isTrue()
        ;

        $this
            ->boolean($bitter->bitOpAnd('test_c', $today, $yesterday)->in(404, 'test_c'))
            ->isFalse()
        ;
    }

    public function testbitOpOr()
    {
        $redisClient = $this->getRedisClient();

        $redisClient->flushdb();

        $bitter = new TestedBitter($redisClient);

        $twoDaysAgo = new Day('drink_a_bitter_beer', new DateTime('2 days ago'));
        $yesterday  = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today      = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->variable($bitter->bitOpOr('test_a', $today, $yesterday))
            ->isIdenticalTo($bitter)
        ;

        $this
            ->boolean($bitter->bitOpOr('test_b', $today, $yesterday)->in(13, 'test_b'))
            ->isTrue()
        ;

        $this
            ->boolean($bitter->bitOpOr('test_c', $today, $twoDaysAgo)->in(13, 'test_c'))
            ->isTrue()
        ;

        $this
            ->boolean($bitter->bitOpOr('test_d', $today, $twoDaysAgo)->in(404, 'test_d'))
            ->isFalse()
        ;
    }

    public function testbitOpXor()
    {
        $redisClient = $this->getRedisClient();

        $redisClient->flushdb();

        $bitter = new TestedBitter($redisClient);

        $yesterday = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today     = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->variable($bitter->bitOpXor('test_a', $today, $yesterday))
            ->isIdenticalTo($bitter)
        ;

        $this
            ->boolean($bitter->bitOpXor('test_b', $today, $yesterday)->in(13, 'test_b'))
            ->isFalse()
        ;

        $this
            ->boolean($bitter->bitOpXor('test_c', $today, $yesterday)->in(404, 'test_c'))
            ->isTrue()
        ;
    }
}
