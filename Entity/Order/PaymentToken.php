<?php

namespace Maci\PageBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token;

class PaymentToken extends Token
{
    /**
     * __toString()
     */
    public function __toString()
    {
        return 'PaymentToken_'.($this->hash ? $this->hash : 'New');
    }
}
