<?php
/**
 * checkout.com-magento2-plugin
 *
 * This Magento 2 extension enables to process payments with Checkout.com (https://api.sandbox.checkout.com/payment).
 *
 * @package checkout.com-magento2-plugin
 * @author Rajesh Kumar (https://github.com/rajeshwws/)
 *
 */
namespace Rajeshwws\CKOtest\Block;

class Success extends \Magento\Framework\View\Element\Template
{
    protected $_ckoHelper;
    protected $_checkoutSession;
    protected $_order;
    protected $_transactionBuilder;
    protected $_orderFactory;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Rajeshwws\CKOtest\Helper\Data $ckoHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        $this->_ckoHelper = $ckoHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->_order = $order;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_orderFactory = $orderFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
    // Use this method to get ID
    public function getRealOrderId()
    {
        $lastorderId = $this->_checkoutSession->getLastOrderId();
        return $lastorderId;
    }

    public function getOrder()
    {
        if ($this->_checkoutSession->getLastRealOrderId()) {
            $order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
            return $order;
        }
        return false;
    }

    public function formatePaymentInfo(){
        $transcationInfo = $this->getTransactionDetails();

        $orderDatamodel = $this->_order->getCollection()->getLastItem();
        $orderId   = $orderDatamodel->getId();
        $order = $this->_order->load($orderId);

        echo '<div class="checkout-success" style="margin-left: 10px;margin-top: -20px;margin-bottom: 30px;">
        <p>Your order number is: <strong>'.$order->getIncrementId().'</strong>.</p>
        <p>Transaction ID: <strong>'.$transcationInfo['id'].'</strong>.</p>
        <p>Reference: <strong>'.$transcationInfo['reference'].'</strong>.</p>
        <p>Approved: <strong>'.$transcationInfo['approved'].'</strong>.</p>
        <p>Status: <strong>'.$transcationInfo['status'].'</strong>.</p>
        <p>We will email you an order confirmation with details and tracking info</p>
        </div>';

    }

    public function getTransactionDetails(){
        $ckoSessionId     =  $this->_checkoutSession->getCkoSessionId();
        if($ckoSessionId){
            $headers  = [
                "Authorization: {$this->_ckoHelper->getSecretKey()}",
                "Content-Type: application/json"
            ];
            $response =  $this->_ckoHelper->buildCurlGetRequest($ckoSessionId, $headers);
            $this->_ckoHelper->logPaymentInfo($response);
            $this->createTransaction($response);
            return $response;
        }
        return [];
    }

    protected function createTransaction($paymentData = array())
    {
        $orderDatamodel = $this->_order->getCollection()->getLastItem();
        $orderId   =   $orderDatamodel->getId();
        if($orderId){
            $order = $this->_order->load($orderId);
            try {
                //get payment object from order object
                $payment = $order->getPayment();
                $payment->setLastTransId($paymentData['id']);
                $payment->setTransactionId($paymentData['id']);
                $payment->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
                );
                $formatedPrice = $order->getBaseCurrency()->formatTxt(
                    $order->getGrandTotal()
                );

                $message = __('The authorized amount is %1.', $formatedPrice);
                //get the object of builder class
                $transaction = $this->_transactionBuilder->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($paymentData['id'])
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
                )
                ->setFailSafe(true)
                //build method creates the transaction and returns the object
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
                $payment->addTransactionCommentsToOrder(
                    $transaction,
                    $message
                );
                $payment->setParentTransactionId(null);
                $payment->save();
                $order->save();

                return  $transaction->save()->getTransactionId();
            } catch (\Exception $e) {
                //log errors here
                $this->_ckoHelper->logPaymentInfo($e->getMessage());
            }
        }
        return $this;
    }
}
