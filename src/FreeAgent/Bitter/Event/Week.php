<?php

namespace FreeAgent\Bitter\Event;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Week extends AbstractEvent implements EventInterface
{
    public function getDateTimeFormated()
    {
        return sprintf('%s-W%s', $this->getDateTime()->format('Y'), $this->getDateTime()->format('W'));
    }
}
