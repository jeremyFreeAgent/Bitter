<?php

namespace Bitter\Event;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Month extends AbstractEvent implements EventInterface
{
    public function getDateTimeFormated()
    {
        return sprintf('%s-%s', $this->getDateTime()->format('Y'), $this->getDateTime()->format('m'));
    }
}
