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
namespace Rajeshwws\CKOtest\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const API_URL = 'https://api.sandbox.checkout.com/payments';
	/**
     * @var StoreManagerInterface
    */
    protected $_storeManager;

    /**
     * @var scopeConfigInterface
    */
    protected $_scopeConfigInterface;

    /**
     * @var curlFactory
    */
    protected $_curlFactory;

    /**
     * @var jsonHelper
    */
    protected $_jsonHelper;


	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfigInterface,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->_storeManager = $storeManager;
		$this->_scopeConfigInterface = $scopeConfigInterface;
        $this->_curlFactory = $curlFactory;
        $this->_jsonHelper  = $jsonHelper;
        parent::__construct($context);
    }

    public function getPublicKey(){
    	return $this->_scopeConfigInterface->getValue(
    			'payment/ckotest/public_key', 
    			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
    		);
    }


    public function getSecretKey(){
    	return $this->_scopeConfigInterface->getValue(
    			'payment/ckotest/secret_key', 
    			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
    		);
    }
    public function getConfigData(){
    	return [
    		'public_key' => $this->getPublicKey(),
    		'secret_key' => $this->getSecretKey()
    	];
    }

    public function logPaymentInfo($info){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/payment.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("- - - - #############start############### - - - -");
        $logger->info($info);
        $logger->info("- - - - ##############End############## - - - -");
    }

    public function buildCurlGetRequest($query, $headers){
        $httpAdapter = $this->_curlFactory->create();
        $httpAdapter->write(
            \Zend_Http_Client::GET,
             self::API_URL.'/'.$query, 
            '1.1', 
            $headers
        );
        $result = $httpAdapter->read();
        $body = \Zend_Http_Response::extractBody($result);
        /* convert JSON to Array */
        $response = $this->_jsonHelper->jsonDecode($body);
        return $response;
    }

    public function buildCurlRequest($headers, $vars){
        /* Create curl factory */
        $httpAdapter = $this->_curlFactory->create();
        /* Forth parameter is POST body */
        $httpAdapter->write(
            \Zend_Http_Client::POST, 
            self::API_URL, 
            '1.1', 
            $headers,
            json_encode($vars)
        );
        $result = $httpAdapter->read();
        $body = \Zend_Http_Response::extractBody($result);
        /* convert JSON to Array */
        $response = $this->_jsonHelper->jsonDecode($body);
        return $response;
    }
}
