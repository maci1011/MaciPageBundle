<?php

namespace Maci\PageBundle\Action;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Guzzle\HttpClientFactory;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Notify;
use Payum\Paypal\Ipn\Api;

use Symfony\Component\HttpFoundation\Request;

use Http\Message\MessageFactory\GuzzleMessageFactory;

use Maci\PageBundle\Entity\PaymentDetails;

class StoreNotificationAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    protected $om;

    protected $sandbox;

    public function __construct(
    	ObjectManager $om,
    	Request $request
    ) {
        $this->om = $om;
        $this->request = $request;
        $this->sandbox = false;
    }

    public function execute($request)
    {
		/** @var $request NotifyRequest */
		if (false == $this->supports($request)) {
		    throw RequestNotSupportedException::createActionNotSupported($this, $request);
		}

		$notification = $request->getNotification();

		$paymentDetails = new PaymentDetails();
		$paymentDetails->setType('paypalIpn');
		$paymentDetails->setDetails($notification);

		$this->om->persist($paymentDetails);
		$this->om->flush();

		// TODO: read sandbox attribute from config
		$api = new Api(['sandbox' => $this->sandbox], HttpClientFactory::create(), new GuzzleMessageFactory());

		// Verify the IPN via PayPal
		if (Api::NOTIFY_VERIFIED !== $api->notifyValidate($request->getNotification())) {
		    throw new NotFoundHttpException('Invalid IPN');
		}

		$model = $request->getModel();

		// Additional Checks
		if (!$this->checkEquality(
		    array(
		        'payer_id' => 'PAYERID',
		        'mc_gross' => 'PAYMENTREQUEST_0_AMT',
		        // maybe more
				// 'item_name' => '';
				// 'item_number' => '';
				// 'payment_status' => '';
				// 'payment_amount' => '';
				// 'payment_currency' => '';
				// 'txn_id' => '';
				// 'receiver_email' => '';
				// 'payer_email' => '';
		    ),
		    $notification,
		    $model
		)) {
		    throw new NotFoundHttpException('Malformed IPN');
		}


		$previousState  = $model['PAYMENTREQUEST_0_PAYMENTSTATUS'];
		$currentState   = $notification['payment_status'];

		if ($previousState !== $currentState) {

		    // ... do something with that state change

		    // $model['PAYMENTREQUEST_0_PAYMENTSTATUS'] = $currentState;

		    // $this->om->persist($model);
		    // $this->om->flush();

		}// else { no state change. Maybe no need to do something. }
    }

    protected function checkEquality($array, $notification, $model)
    {
        foreach ($array as $key => $value) {
            if ($notification[$key] !== $model[$value]) {
                return false;
            }
        }
        return true;
    }

    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
