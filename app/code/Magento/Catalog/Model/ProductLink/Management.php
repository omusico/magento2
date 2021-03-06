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

namespace Magento\Catalog\Model\ProductLink;

use Magento\Catalog\Api\Data;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class Management implements \Magento\Catalog\Api\ProductLinkManagementInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var CollectionProvider
     */
    protected $entityCollectionProvider;

    /**
     * @var LinksInitializer
     */
    protected $linkInitializer;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkDataBuilder
     */
    protected $productLinkBuilder;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $productResource;

    /**
     * @var \Magento\Framework\Api\AttributeValueBuilder
     */
    protected $valueBuilder;

    /**
     * @var \Magento\Catalog\Model\Product\LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param CollectionProvider $collectionProvider
     * @param Data\ProductLinkDataBuilder $productLinkBuilder
     * @param LinksInitializer $linkInitializer
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Framework\Api\AttributeValueBuilder $valueBuilder
     * @param \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CollectionProvider $collectionProvider,
        \Magento\Catalog\Api\Data\ProductLinkDataBuilder $productLinkBuilder,
        LinksInitializer $linkInitializer,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Framework\Api\AttributeValueBuilder $valueBuilder,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider
    ) {
        $this->productRepository = $productRepository;
        $this->entityCollectionProvider = $collectionProvider;
        $this->productLinkBuilder = $productLinkBuilder;
        $this->productResource = $productResource;
        $this->linkInitializer = $linkInitializer;
        $this->valueBuilder = $valueBuilder;
        $this->linkTypeProvider = $linkTypeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedItemsByType($productSku, $type)
    {
        $output = [];
        $product = $this->productRepository->get($productSku);
        try {
            $collection = $this->entityCollectionProvider->getCollection($product, $type);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException('Unknown link type: ' . (string)$type);
        }
        foreach ($collection as $item) {
            $data = [
                'product_sku' => $product->getSku(),
                'link_type' => $type,
                'linked_product_sku' => $item['sku'],
                'linked_product_type' => $item['type'],
                'position' => $item['position'],
            ];
            $this->productLinkBuilder->populateWithArray($data);
            if (isset($item['custom_attributes'])) {
                foreach ($item['custom_attributes'] as $option) {
                    $this->productLinkBuilder->setCustomAttribute(
                        $option['attribute_code'],
                        $option['value']
                    );
                }
            }
            $output[] = $this->productLinkBuilder->create();
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductLinks($productSku, $type, array $items)
    {
        $linkTypes = $this->linkTypeProvider->getLinkTypes();

        if (!isset($linkTypes[$type])) {
            throw new NoSuchEntityException(
                sprintf("Provided link type \"%s\" does not exist", $type)
            );
        }

        $product = $this->productRepository->get($productSku);
        $assignedSkuList = [];
        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $link */
        foreach ($items as $link) {
            $assignedSkuList[] = $link->getLinkedProductSku();
        }
        $linkedProductIds = $this->productResource->getProductsIdsBySkus($assignedSkuList);

        $links = [];
        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface[] $items*/
        foreach ($items as $link) {
            $data = $link->__toArray();
            $linkedSku = $link->getLinkedProductSku();
            if (!isset($linkedProductIds[$linkedSku])) {
                throw new NoSuchEntityException(
                    sprintf("Product with SKU \"%s\" does not exist", $linkedSku)
                );
            }
            $data['product_id'] = $linkedProductIds[$linkedSku];
            $links[$linkedProductIds[$linkedSku]] = $data;
        }
        $this->linkInitializer->initializeLinks($product, [$type => $links]);
        try {
            $product->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException('Invalid data provided for linked products');
        }
        return true;
    }
}
