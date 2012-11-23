<?php

namespace FreeAgent\Bitter\tests\units;

require_once __DIR__ . '/../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use FreeAgent\Bitter\Bitter as TestedBitter;
use FreeAgent\Bitter\Event\Day;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Bitter extends atoum\test
{
    private function getRedisClient()
    {
        return new \Predis\Client();
    }

    private function getPrefixKey()
    {
        return 'test_bitter:';
    }

    private function getPrefixTempKey()
    {
        return 'test_bitter_temp:';
    }

    private function removeAll()
    {
        $keys_chunk = array_chunk($this->getRedisClient()->keys($this->getPrefixKey() . '*'), 100);

        foreach ($keys_chunk as $keys) {
            $this->getRedisClient()->del($keys);
        }

        $keys_chunk = array_chunk($this->getRedisClient()->keys($this->getPrefixTempKey() . '*'), 100);

        foreach ($keys_chunk as $keys) {
            $this->getRedisClient()->del($keys);
        }
    }

    public function testConstruct()
    {
        $redisClient = $this->getRedisClient();

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this
            ->variable($bitter->getRedisClient())
            ->isIdenticalTo($redisClient)
        ;
    }

    public function testMarkEvent()
    {
        $redisClient = $this->getRedisClient();

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll();

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

        $this->removeAll();

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

        $this->removeAll();
    }

    public function testbitOpAnd()
    {
        $redisClient = $this->getRedisClient();

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll();

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

        $this->removeAll();
    }

    public function testbitOpOr()
    {
        $redisClient = $this->getRedisClient();

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll();

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

        $this->removeAll();
    }

    public function testbitOpXor()
    {
        $redisClient = $this->getRedisClient();

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll();

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

        $this->removeAll();
    }

    public function testRemoveAll()
    {
        $redisClient = $this->getRedisClient();

        $this->removeAll();

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->isEmpty()
        ;

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $yesterday = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today     = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->isNotEmpty()
        ;

        $bitter->removeAll();

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->strictlyContains($this->getPrefixKey() . 'keys')
        ;


        $this->removeAll();
    }

    public function testRemoveTemp()
    {
        $redisClient = $this->getRedisClient();

        $keys_chunk = array_chunk($redisClient->keys($this->getPrefixKey() . '*'), 100);

        foreach ($keys_chunk as $keys) {
            $redisClient->del($keys);
        }

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->isEmpty()
        ;

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $yesterday = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today     = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $bitter->bitOpOr('test_b', $today, $yesterday);

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->isNotEmpty()
        ;

        $this
            ->array($redisClient->keys($this->getPrefixTempKey() . '*'))
            ->isNotEmpty()
        ;

        $bitter->removeTemp();

        $this
            ->array($redisClient->keys($this->getPrefixTempKey() . '*'))
            ->strictlyContains($this->getPrefixTempKey() . 'keys')
        ;

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->isNotEmpty()
        ;

        // Expire timeout
        $this->removeAll();

        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey(), 2);
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $bitter->bitOpOr('test_b', $today, $yesterday);

        $this
            ->array($redisClient->keys($this->getPrefixTempKey() . '*'))
            ->isNotEmpty()
        ;

        sleep(3);

        $this
            ->array($redisClient->keys($this->getPrefixTempKey() . '*'))
            ->strictlyContains($this->getPrefixTempKey() . 'keys')
        ;

        $this->removeAll();
    }
}
