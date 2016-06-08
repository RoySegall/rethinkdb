<?php

namespace r\Queries\Math;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Round extends ValuedQuery
{
    public function __construct($value)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($value));
    }

    protected function getTermType()
    {
        return TermTermType::PB_ROUND;
    }
}
