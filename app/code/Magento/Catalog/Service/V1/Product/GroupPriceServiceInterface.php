<?php
/**
 *
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
namespace Magento\Catalog\Service\V1\Product;

/**
 * @todo remove this interface
 * @see \Magento\Catalog\Api\ProductGroupPriceManagementInterface
 */
interface GroupPriceServiceInterface
{
    /**
     * Set group price for product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Data\Product\GroupPrice $price
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @deprecated
     * @see \Magento\Catalog\Api\ProductGroupPriceManagementInterface::add
     */
    public function set($productSku, \Magento\Catalog\Service\V1\Data\Product\GroupPrice $price);

    /**
     * Remove group price from product
     *
     * @param string $productSku
     * @param int $customerGroupId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @deprecated
     * @see \Magento\Catalog\Api\ProductGroupPriceManagementInterface::remove
     */
    public function delete($productSku, $customerGroupId);

    /**
     * Retrieve list of product prices
     *
     * @param string $productSku
     * @return \Magento\Catalog\Service\V1\Data\Product\GroupPrice[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\ProductGroupPriceManagementInterface::getList
     */
    public function getList($productSku);
}
