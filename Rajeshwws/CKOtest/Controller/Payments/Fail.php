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

class Fail extends \Magento\Framework\App\Action\Action{

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory
	)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
        // Log the payment error

        // Handle the failed order

        // Restore the quote

        // Display the message - (The transaction could not be processed.)

        // Return to the cart if the failureUrl set then we will response failureUrl url to return cart

         return $this->_redirect('checkout/cart', ['_secure' => true]);
	}

}