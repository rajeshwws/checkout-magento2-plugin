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
namespace Rajeshwws\CKOtest\Controller\Payments;

class Index extends \Magento\Framework\App\Action\Action{

	const METHOD_CODE = 'ckotest';

	protected $_pageFactory;
        protected $_checkoutSession;
        protected $_quoteManagement;
        protected $_ckoHelper;

	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\View\Result\PageFactory $pageFactory,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Quote\Model\QuoteManagement $quoteManagement,
            \Rajeshwws\CKOtest\Helper\Data $helper
	)
	{
	     $this->_pageFactory   = $pageFactory;
	     $this->_checkoutSession = $checkoutSession;
             $this->_quoteManagement = $quoteManagement;
             $this->_ckoHelper = $helper;
	     return parent::__construct($context);
	}

	public function execute()
	{
		$params = $this->getRequest()->getParams();
		$token  = $this->getRequest()->getParam('cko-torkn');
		if($token){
			//creating here magento order
			$this->createOrder();
			$redirectUrl = $this->processRequest($token);
			if($redirectUrl){
				$this->_redirect($redirectUrl);
			}else{
				$this->_redirect('checkout/cart');
			}
		}else{
			$this->_redirect('checkout/cart');
		}
	}

	protected function createOrder(){

		if($this->_checkoutSession->getQuote()->getId()){
			$quote = $this->_checkoutSession->getQuote();
			//get payment information
			$payment = $quote->getPayment();
			$payment->setMethod(self::METHOD_CODE);

			$billingAddress = $quote->getBillingAddress();
			$billingAddress->setShouldIgnoreValidation(true);
			if(!$quote->getIsVirtual()) {
	            $shippingAddress = $quote->getShippingAddress();
	            $shippingAddress->setShouldIgnoreValidation(true);

	            if (!$billingAddress->getEmail()) {
	                $billingAddress->setSameAsBilling(1);
	            }
	        }
		$quote->collectTotals()->save();
                $this->_checkoutSession->setLastSuccessQuoteId($quote->getId());
                $this->_quoteManagement->submit($quote);

		}
		return $this;
	}

	protected function processRequest($token){
		if($token){
			$data = [
				'source' => [
					'type'  => 'token',
					'token' => $token
				],
				'amount' => (float) $this->_checkoutSession->getQuote()->getGrandTotal(),
				'reference' => 'rajeshwws_cko_mage',
				'currency'  => 'USD',
				'success_url' => $this->_url->getUrl('ckotest/payments/success'),
				'failure_url' => $this->_url->getUrl('ckotest/payments/fail'),
				'3ds'    => [
					'enabled' => true
				]
			];
			$this->_ckoHelper->logPaymentInfo($data);
			$headers = [
				"Authorization: {$this->_ckoHelper->getSecretKey()}",
				"Content-Type: application/json"
			];
			$response = $this->_ckoHelper->buildCurlRequest($headers,$data);
			$this->_ckoHelper->logPaymentInfo($response);
			if(is_array($response) && $response['_links']['redirect']['href']){
			     return $response['_links']['redirect']['href'];
			}
		}
		return;
	}
}
