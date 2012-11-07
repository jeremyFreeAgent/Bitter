<?php

namespace Bitter;

use \DateTime;
use Bitter\Event\Month;
use Bitter\Event\Week;
use Bitter\Event\Day;
use Bitter\Event\Hour;
use Bitter\Event\EventInterface;

class Bitter
{
    private $redisClient;

    public function __construct($redisClient)
    {
        $this->setRedisClient($redisClient);
    }

    /**
     * Get the Redis client
     *
     * @return The Redis client
     */
    public function getRedisClient() {
        return $this->redisClient;
    }

    /**
     * Set the Redis client
     *
     * @param [type] $newredisClient The Redis client
     */
    public function setRedisClient($redisClient) {
        $this->redisClient = $redisClient;

        return $this;
    }

    /**
     * Marks an event for hours, days, weeks and months
     *
     * @param  string   $eventName The name of the event, could be "active" or "new_signups"
     * @param  integer  $id        An unique id, typically user id. The id should not be huge, read Redis documentation why (bitmaps)
     * @param  DateTime $dateTime  Which date should be used as a reference point, default is now
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
            $this->getRedisClient()->setbit($event->getKey(), $id, 1);
        }
    }

    /**
     * Makes it possible to see if an id has been marked
     *
     * @param  EventInterface $event The event
     * @param  integer        $id    An unique id
     * @return boolean               True if the id has been marked
     */
    public function contain(EventInterface $event, $id)
    {
        return (bool) $this->getRedisClient()->getbit($event->getKey(), $id);
    }

    public function count(EventInterface $event)
    {
        return (int) $this->getRedisClient()->bitcount($event->getKey());
    }
}
