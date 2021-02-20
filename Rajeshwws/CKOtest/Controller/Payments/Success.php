<?php
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
