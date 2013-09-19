<?php

namespace FreeAgent\Bitter;

use \DateTime;
use \Exception;
use FreeAgent\Bitter\Date\DatePeriod;
use FreeAgent\Bitter\Event\Year;
use FreeAgent\Bitter\Event\Month;
use FreeAgent\Bitter\Event\Week;
use FreeAgent\Bitter\Event\Day;
use FreeAgent\Bitter\Event\Hour;
use FreeAgent\Bitter\Event\EventInterface;

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
            new Year($eventName, $dateTime),
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

        return $this;
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

    /**
     * Counts the number of marks
     *
     * @param  mixed   $key The key or the event
     * @return integer The value of the count result
     */
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

    public function bitDateRange($key, $destKey, DateTime $from, DateTime $to)
    {
        if ($from > $to) {
            throw new Exception("DateTime from (" . $from->format('Y-m-d H:i:s') . ") must be anterior to DateTime to (" . $to->format('Y-m-d H:i:s') . ").");
        }

        $from = clone $from;
        $to = clone $to;

        $this->getRedisClient()->del($this->prefixTempKey . $destKey);

        // Hours
        $hoursFrom = DatePeriod::createForHour($from, $to, DatePeriod::CREATE_FROM);
        foreach ($hoursFrom as $date) {
            $this->bitOpOr($destKey, new Hour($key, $date), $destKey);
        }
        $hoursTo = DatePeriod::createForHour($from, $to, DatePeriod::CREATE_TO);
        if (array_diff($hoursTo->toArray(true), $hoursFrom->toArray(true)) !== array_diff($hoursFrom->toArray(true), $hoursTo->toArray(true))) {
            foreach ($hoursTo as $date) {
                $this->bitOpOr($destKey, new Hour($key, $date), $destKey);
            }
        }

        // Days
        $daysFrom = DatePeriod::createForDay($from, $to, DatePeriod::CREATE_FROM);
        foreach ($daysFrom as $date) {
            $this->bitOpOr($destKey, new Day($key, $date), $destKey);
        }
        $daysTo = DatePeriod::createForDay($from, $to, DatePeriod::CREATE_TO);
        if (array_diff($daysTo->toArray(true), $daysFrom->toArray(true)) !== array_diff($daysFrom->toArray(true), $daysTo->toArray(true))) {
            foreach ($daysTo as $date) {
                $this->bitOpOr($destKey, new Day($key, $date), $destKey);
            }
        }

        // Months
        $monthsFrom = DatePeriod::createForMonth($from, $to, DatePeriod::CREATE_FROM);
        foreach ($monthsFrom as $date) {
            $this->bitOpOr($destKey, new Month($key, $date), $destKey);
        }
        $monthsTo = DatePeriod::createForMonth($from, $to, DatePeriod::CREATE_TO);
        if (array_diff($monthsTo->toArray(true), $monthsFrom->toArray(true)) !== array_diff($monthsFrom->toArray(true), $monthsTo->toArray(true))) {
            foreach ($monthsTo as $date) {
                $this->bitOpOr($destKey, new Month($key, $date), $destKey);
            }
        }

        // Years
        $years = DatePeriod::createForYear($from, $to);
        foreach ($years as $date) {
            $this->bitOpOr($destKey, new Year($key, $date), $destKey);
        }

        $this->getRedisClient()->sadd($this->prefixTempKey . 'keys', $destKey);
        $this->getRedisClient()->expire($destKey, $this->expireTimeout);

        return $this;
    }

    /**
     * Removes all Bitter keys
     */
    public function removeAll()
    {
        $keys_chunk = array_chunk($this->getRedisClient()->smembers($this->prefixKey . 'keys'), 100);

        foreach ($keys_chunk as $keys) {
            $this->getRedisClient()->del($keys);
        }

        return $this;
    }

    /**
     * Removes all Bitter temp keys
     */
    public function removeTemp()
    {
        $keys_chunk = array_chunk($this->getRedisClient()->smembers($this->prefixTempKey . 'keys'), 100);

        foreach ($keys_chunk as $keys) {
            $this->getRedisClient()->del($keys);
        }

        return $this;
    }
}
