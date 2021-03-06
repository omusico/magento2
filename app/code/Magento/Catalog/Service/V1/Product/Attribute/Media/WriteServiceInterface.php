<?php
/**
 * Product Media Attribute Write Service
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
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

use \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry;
use \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryContent;

/**
 * @deprecated
 */
interface WriteServiceInterface
{
    /**
     * Create new gallery entry
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry $entry
     * @param \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryContent $entryContent
     * @param int $storeId
     * @return int gallery entry ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface::create
     */
    public function create($productSku, GalleryEntry $entry, GalleryEntryContent $entryContent, $storeId = 0);

    /**
     * Update gallery entry
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry $entry
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface::update
     */
    public function update($productSku, GalleryEntry $entry, $storeId = 0);

    /**
     * Remove gallery entry
     *
     * @param string $productSku
     * @param int $entryId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface::remove
     */
    public function delete($productSku, $entryId);
}
