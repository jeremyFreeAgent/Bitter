<?php

namespace Bitter\Event;

class Month extends AbstractEvent implements EventInterface
{
    public function getDateTimeFormated()
    {
        return sprintf('%s-%s', $this->getDateTime()->format('Y'), $this->getDateTime()->format('m'));
    }
}
