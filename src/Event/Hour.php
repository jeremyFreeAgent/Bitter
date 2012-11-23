<?php

namespace FreeAgent\Bitter\Event;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Hour extends AbstractEvent implements EventInterface
{
    public function getDateTimeFormated()
    {
        return sprintf('%s-%s-%s-%s', $this->getDateTime()->format('Y'), $this->getDateTime()->format('m'), $this->getDateTime()->format('d'), $this->getDateTime()->format('H'));
    }
}
