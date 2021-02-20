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
namespace Rajeshwws\CKOtest\Model;
 
/**
 * Pay In Store payment method model
 */
class CKOtestMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'ckotest';

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Authorize payment abstract method
     *
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if (!$this->canAuthorize()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The authorize action is not available.'));
        }
        return $this;
    }
}