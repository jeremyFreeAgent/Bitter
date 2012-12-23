<?php

namespace FreeAgent\Bitter\Event;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Year extends AbstractEvent implements EventInterface
{
    public function getDateTimeFormated()
    {
        return sprintf('%s', $this->getDateTime()->format('Y'));
    }
}
