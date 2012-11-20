<?php

namespace Bitter;

use \DateTime;
use Bitter\Event\Month;
use Bitter\Event\Week;
use Bitter\Event\Day;
use Bitter\Event\Hour;
use Bitter\Event\EventInterface;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Bitter
{
    private $redisClient;
    private $prefixKey;
    private $prefixTempKey;
    private $expireTimeout;

    public function __construct($redisClient, $prefixKey = 'bitter:', $prefixTempKey = 'bitter_temp:', $expireTimeout = 60)
    {
        $this->setRedisClient($redisClient);
        $this->prefixKey     = $prefixKey;
        $this->prefixTempKey = $prefixTempKey;
        $this->expireTimeout = $expireTimeout;
    }

    /**
     * Get the Redis client
     *
     * @return The Redis client
     */
    public function getRedisClient()
    {
        return $this->redisClient;
    }

    /**
     * Set the Redis client
     *
     * @param [type] $newredisClient The Redis client
     */
    public function setRedisClient($redisClient)
    {
        $this->redisClient = $redisClient;

        return $this;
    }

    /**
     * Marks an event for hours, days, weeks and months
     *
     * @param string   $eventName The name of the event, could be "active" or "new_signups"
     * @param integer  $id        An unique id, typically user id. The id should not be huge, read Redis documentation why (bitmaps)
     * @param DateTime $dateTime  Which date should be used as a reference point, default is now
     */
    public function mark($eventName, $id, DateTime $dateTime = null)
    {
        $dateTime = is_null($dateTime) ? new DateTime : $dateTime;

        $eventData = array(
            new Month($eventName, $dateTime),
            new Week($eventName, $dateTime),
            new Day($eventName, $dateTime),
            new Hour($eventName, $dateTime),
        );

        foreach ($eventData as $event) {
            $key = $this->prefixKey . $event->getKey();
            $this->getRedisClient()->setbit($key, $id, 1);
            $this->getRedisClient()->sadd($this->prefixKey . 'keys', $key);
        }
    }

    /**
     * Makes it possible to see if an id has been marked
     *
     * @param  integer $id  An unique id
     * @param  mixed   $key The key or the event
     * @return boolean True if the id has been marked
     */
    public function in($id, $key)
    {
        $key = $key instanceof EventInterface ? $this->prefixKey . $key->getKey() : $this->prefixTempKey . $key;

        return (bool) $this->getRedisClient()->getbit($key, $id);
    }

    public function count($key)
    {
        $key = $key instanceof EventInterface ? $this->prefixKey . $key->getKey() : $this->prefixTempKey . $key;

        return (int) $this->getRedisClient()->bitcount($key);
    }

    private function bitOp($op, $destKey, $keyOne, $keyTwo)
    {
        $keyOne = $keyOne instanceof EventInterface ? $this->prefixKey . $keyOne->getKey() : $this->prefixTempKey . $keyOne;
        $keyTwo = $keyTwo instanceof EventInterface ? $this->prefixKey . $keyTwo->getKey() : $this->prefixTempKey . $keyTwo;

        $this->getRedisClient()->bitop($op, $this->prefixTempKey . $destKey, $keyOne, $keyTwo);
        $this->getRedisClient()->sadd($this->prefixTempKey . 'keys', $destKey);
        $this->getRedisClient()->expire($destKey, $this->expireTimeout);

        return $this;
    }

    public function bitOpAnd($destKey, $keyOne, $keyTwo)
    {
        return $this->bitOp('AND', $destKey, $keyOne, $keyTwo);
    }

    public function bitOpOr($destKey, $keyOne, $keyTwo)
    {
        return $this->bitOp('OR', $destKey, $keyOne, $keyTwo);
    }

    public function bitOpXor($destKey, $keyOne, $keyTwo)
    {
        return $this->bitOp('XOR', $destKey, $keyOne, $keyTwo);
    }

    public function removeAll()
    {
        $keys_chunk = array_chunk($this->getRedisClient()->smembers($this->prefixKey . 'keys'), 100);

        foreach ($keys_chunk as $keys) {
            $this->getRedisClient()->del($keys);
        }
    }

    public function removeTemp()
    {
        $keys_chunk = array_chunk($this->getRedisClient()->smembers($this->prefixTempKey . 'keys'), 100);

        foreach ($keys_chunk as $keys) {
            $this->getRedisClient()->del($keys);
        }
    }
}
