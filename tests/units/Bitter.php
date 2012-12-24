<?php

namespace FreeAgent\Bitter\tests\units;

require_once __DIR__ . '/../../vendor/autoload.php';

use \mageekguy\atoum;
use \DateTime;
use FreeAgent\Bitter\Bitter as TestedBitter;
use FreeAgent\Bitter\Event\Day;

/**
 * @engine isolate
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Bitter extends atoum\test
{
    public function dataProviderTestedClients()
    {
        $clients = array(
            new \Predis\Client(),
        );

        if (class_exists('\Redis')) {
            $conn = new \Redis();
            $conn->connect('127.0.0.1');

            $clients[] = $conn;
        }

        return $clients;
    }

    private function getPrefixKey()
    {
        return 'test_bitter:';
    }

    private function getPrefixTempKey()
    {
        return 'test_bitter_temp:';
    }

    private function removeAll($redisClient)
    {
        $keys_chunk = array_chunk($redisClient->keys($this->getPrefixKey() . '*'), 100);

        foreach ($keys_chunk as $keys) {
            $redisClient->del($keys);
        }

        $keys_chunk = array_chunk($redisClient->keys($this->getPrefixTempKey() . '*'), 100);

        foreach ($keys_chunk as $keys) {
            $redisClient->del($keys);
        }
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testConstruct($redisClient)
    {
        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this
            ->object($bitter->getRedisClient())
            ->isIdenticalTo($redisClient)
        ;
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testMarkEvent($redisClient)
    {
        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll($redisClient);

        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-11-06 15:30:45');

        $day = new Day('drink_a_bitter_beer', $dateTime);

        $this
            ->integer($bitter->count($day))
            ->isIdenticalTo(0)
        ;
        $this
            ->boolean($bitter->in(404, $day))
            ->isFalse()
        ;

        $this
            ->object($bitter->mark('drink_a_bitter_beer', 404, $dateTime))
            ->isIdenticalTo($bitter)
        ;

        $this
            ->integer($bitter->count($day))
            ->isIdenticalTo(1)
        ;
        $this
            ->boolean($bitter->in(404, $day))
            ->isTrue()
        ;

        // Adding it a second time with the same dateTime !
        $bitter->mark('drink_a_bitter_beer', 404, $dateTime);

        $this
            ->integer($bitter->count($day))
            ->isIdenticalTo(1)
        ;
        $this
            ->boolean($bitter->in(404, $day))
            ->isTrue()
        ;

        $this->removeAll($redisClient);

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

        $this->removeAll($redisClient);
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testbitOpAnd($redisClient)
    {
        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll($redisClient);

        $yesterday = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today     = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->object($bitter->bitOpAnd('test_a', $today, $yesterday))
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

        $this->removeAll($redisClient);
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testbitOpOr($redisClient)
    {
        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll($redisClient);

        $twoDaysAgo = new Day('drink_a_bitter_beer', new DateTime('2 days ago'));
        $yesterday  = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today      = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->object($bitter->bitOpOr('test_a', $today, $yesterday))
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

        $this->removeAll($redisClient);
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testbitOpXor($redisClient)
    {
        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll($redisClient);

        $yesterday = new Day('drink_a_bitter_beer', new DateTime('yesterday'));
        $today     = new Day('drink_a_bitter_beer', new DateTime('today'));

        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('today'));
        $bitter->mark('drink_a_bitter_beer', 13, new DateTime('yesterday'));
        $bitter->mark('drink_a_bitter_beer', 404, new DateTime('yesterday'));

        $this
            ->object($bitter->bitOpXor('test_a', $today, $yesterday))
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

        $this->removeAll($redisClient);
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testBitDateRange($redisClient)
    {
        $bitter = new TestedBitter($redisClient, $this->getPrefixKey(), $this->getPrefixTempKey());

        $this->removeAll($redisClient);

        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2011-11-06 15:30:45');
        $bitter->mark('drink_a_bitter_beer', 1, $dateTime);
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2012-10-12 15:30:45');
        $bitter->mark('drink_a_bitter_beer', 2, $dateTime);

        $this
            ->if($from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-10-05 15:30:45'))
            ->and($to = DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-07 15:30:45'))
            ->and($bitter->bitDateRange('drink_a_bitter_beer', 'test_create_date_period', $from, $to))
            ->then()
            ->object($bitter->bitDateRange('drink_a_bitter_beer', 'test_create_date_period', $from, $to))
            ->isIdenticalTo($bitter)
        ;

        $this
            ->if($prefixKey = $this->getPrefixKey())
            ->and($prefixTempKey = $this->getPrefixTempKey())
            ->exception(
                function() use ($redisClient, $prefixKey, $prefixTempKey) {
                    $bitter = new TestedBitter($redisClient, $prefixKey, $prefixTempKey);
                    $from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-07 15:30:45');
                    $to = DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-07 14:30:45');
                    $bitter->bitDateRange('drink_a_bitter_beer', 'test_create_date_period', $from, $to);
                }
            )
            ->hasMessage("DateTime from (2012-12-07 15:30:45) must be anterior to DateTime to (2012-12-07 14:30:45).")
        ;

        $this
            ->if($from = DateTime::createFromFormat('Y-m-d H:i:s', '2010-10-05 20:30:45'))
            ->and($to = DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-07 12:30:45'))
            ->and($bitter->bitDateRange('drink_a_bitter_beer', 'test_create_date_period', $from, $to))
            ->then()
            ->boolean($bitter->in(1, 'test_create_date_period'))
            ->isTrue()
            ->boolean($bitter->in(2, 'test_create_date_period'))
            ->isTrue()
            ->integer($bitter->count('test_create_date_period'))
            ->isEqualTo(2)
        ;

        $this
            ->if($from = DateTime::createFromFormat('Y-m-d H:i:s', '2012-09-05 20:30:45'))
            ->and($to = DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-07 12:30:45'))
            ->and($bitter->bitDateRange('drink_a_bitter_beer', 'test_create_date_period', $from, $to))
            ->then()
            ->boolean($bitter->in(1, 'test_create_date_period'))
            ->isFalse()
            ->boolean($bitter->in(2, 'test_create_date_period'))
            ->isTrue()
            ->integer($bitter->count('test_create_date_period'))
            ->isEqualTo(1)
        ;

        $this->removeAll($redisClient);
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testRemoveAll($redisClient)
    {
        $this->removeAll($redisClient);

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

        $this
            ->object($bitter->removeAll())
            ->isIdenticalTo($bitter)
        ;

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->strictlyContains($this->getPrefixKey() . 'keys')
        ;


        $this->removeAll($redisClient);
    }

    /**
     * @dataProvider dataProviderTestedClients
     */
    public function testRemoveTemp($redisClient)
    {
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

        $this
            ->object($bitter->removeTemp())
            ->isIdenticalTo($bitter)
        ;

        $this
            ->array($redisClient->keys($this->getPrefixTempKey() . '*'))
            ->strictlyContains($this->getPrefixTempKey() . 'keys')
        ;

        $this
            ->array($redisClient->keys($this->getPrefixKey() . '*'))
            ->isNotEmpty()
        ;

        // Expire timeout
        $this->removeAll($redisClient);

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

        $this->removeAll($redisClient);
    }
}
