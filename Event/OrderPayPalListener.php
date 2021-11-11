<?php

namespace Maci\PageBundle\Event;

use Orderly\PayPalIpnBundle\Event\PayPalEvent;
use Doctrine\Common\Persistence\ObjectManager;

use Maci\PageBundle\Entity\Transaction;
use Maci\PageBundle\Entity\Order\Order;


class OrderPayPalListener {

    private $om;

    public function __construct(ObjectManager $om) {

        $this->om = $om;

    }

    public function onIPNReceive(PayPalEvent $event) {

        // $ipn = $event->getIPN();

        // $ipnOrder = $ipn->getOrder();

    }
}
