<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Payum\Core\Request\GetHumanStatus;

class PaymentController extends AbstractController
{

    public function prepareAction() 
    {
        $gatewayName = 'offline';
        
        $storage = $this->get('payum')->getStorage('Maci\PageBundle\Entity\Payment');
        
        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(123); // 1.23 EUR
        $payment->setDescription('A description');
        $payment->setClientId('anId');
        $payment->setClientEmail('foo@example.com');
        
        $storage->update($payment);
        
        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName, 
            $payment, 
            'done' // the route to redirect after capture
        );
        
        return $this->redirect($captureToken->getTargetUrl());    
    }

    public function doneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);
        
        $gateway = $this->get('payum')->getGateway($token->getGatewayName());
        
        // You can invalidate the token, so that the URL cannot be requested any more:
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);
        
        // Once you have the token, you can get the payment entity from the storage directly. 
        // $identity = $token->getDetails();
        // $payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);
        
        // Or Payum can fetch the entity for you while executing a request (preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();
        
        // Now you have order and payment status
        
        return new JsonResponse(array(
            'status' => $status->getValue(),
            'payment' => array(
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ),
        ));
    }

}
