<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Tax\Model\Sales\Order;

/**
 * @method \Magento\Tax\Model\Resource\Sales\Order\Tax _getResource()
 * @method \Magento\Tax\Model\Resource\Sales\Order\Tax getResource()
 * @method int getOrderId()
 * @method \Magento\Tax\Model\Sales\Order\Tax setOrderId(int $value)
 * @method \Magento\Tax\Model\Sales\Order\Tax setCode(string $value)
 * @method \Magento\Tax\Model\Sales\Order\Tax setTitle(string $value)
 * @method \Magento\Tax\Model\Sales\Order\Tax setPercent(float $value)
 * @method \Magento\Tax\Model\Sales\Order\Tax setAmount(float $value)
 * @method int getPriority()
 * @method \Magento\Tax\Model\Sales\Order\Tax setPriority(int $value)
 * @method int getPosition()
 * @method \Magento\Tax\Model\Sales\Order\Tax setPosition(int $value)
 * @method \Magento\Tax\Model\Sales\Order\Tax setBaseAmount(float $value)
 * @method int getProcess()
 * @method \Magento\Tax\Model\Sales\Order\Tax setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method \Magento\Tax\Model\Sales\Order\Tax setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method \Magento\Tax\Model\Sales\Order\Tax setHidden(int $value)
 * @codeCoverageIgnore
 */
class Tax extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Tax\Model\Resource\Sales\Order\Tax');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getPercent()
    {
        return $this->getData(self::KEY_PERCENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::KEY_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAmount()
    {
        return $this->getData(self::KEY_BASE_AMOUNT);
    }
}
