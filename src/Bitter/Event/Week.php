<?php

namespace Bitter\Event;

class Week extends AbstractEvent implements EventInterface
{
    public function getDateTimeFormated()
    {
        return sprintf('%s-W%s', $this->getDateTime()->format('Y'), $this->getDateTime()->format('W'));
    }
}
