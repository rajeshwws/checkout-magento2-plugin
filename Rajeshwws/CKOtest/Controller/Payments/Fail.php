<?php
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
		//redirect to display failure
	}

}