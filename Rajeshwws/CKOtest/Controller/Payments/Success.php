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

class Success extends \Magento\Framework\App\Action\Action{

    protected  $_checkoutSession;
    protected $_ckoHelper;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Rajeshwws\CKOtest\Helper\Data $ckoHelper
	)
	{
		$this->_pageFactory = $pageFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_ckoHelper = $ckoHelper;
		return parent::__construct($context);
	}

	public function execute()
	{
		$ckoSessionId     = $this->getRequest()->getParam('cko-session-id');
		if($ckoSessionId){
			$this->_ckoHelper->logPaymentInfo("ckoSessionId :- ".$ckoSessionId);
            $this->_checkoutSession->setCkoSessionId($ckoSessionId);
			return $this->_redirect('checkout/onepage/success', ['_secure' => true]);
		}
		return $this->_redirect('checkout/cart');
	}
}
