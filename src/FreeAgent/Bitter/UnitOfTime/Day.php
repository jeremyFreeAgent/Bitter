<?php

namespace FreeAgent\Bitter\UnitOfTime;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Day extends AbstractUnitOfTime implements UnitOfTimeInterface
{
    public function getDateTimeFormated()
    {
        return sprintf('%s-%s-%s', $this->getDateTime()->format('Y'), $this->getDateTime()->format('m'), $this->getDateTime()->format('d'));
    }
}
