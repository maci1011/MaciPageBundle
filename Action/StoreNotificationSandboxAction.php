<?php

namespace Maci\PageBundle\Action;

use Doctrine\Common\Persistence\ObjectManager;
use Payum\Core\Bridge\Guzzle\HttpClientFactory;
use Maci\PageBundle\Action\StoreNotificationAction;

use Http\Message\MessageFactory\GuzzleMessageFactory;

class StoreNotificationSandboxAction extends StoreNotificationAction
{

    public function __construct(
    	ObjectManager $om
    ) {
        $this->om = $om;
        $this->sandbox = true;

    }

}
