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
namespace Magento\Framework\Data\CollectionBuilder;

use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Api\ExtensibleObjectBuilder;
use Magento\Framework\Api\MetadataServiceInterface;

/**
 * Builder for FilterGroup Data.
 */
class FilterGroupBuilder extends ExtensibleObjectBuilder
{
    /**
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeDataBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param FilterBuilder $filterBuilder
     * @param null $modelClassInterface
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeDataBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        FilterBuilder $filterBuilder,
        $modelClassInterface = null
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService, $modelClassInterface);
        $this->_filterBuilder = $filterBuilder;
    }

    /**
     * Add filter
     *
     * @param \Magento\Framework\Api\Filter $filter
     * @return $this
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->data[FilterGroup::FILTERS][] = $filter;
        return $this;
    }

    /**
     * Set filters
     *
     * @param \Magento\Framework\Api\Filter[] $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        return $this->_set(FilterGroup::FILTERS, $filters);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (isset($data[FilterGroup::FILTERS])) {
            $filters = [];
            foreach ($data[FilterGroup::FILTERS] as $filter) {
                $filters[] = $this->_filterBuilder->populateWithArray($filter)->create();
            }
            $data[FilterGroup::FILTERS] = $filters;
        }
        return parent::_setDataValues($data);
    }
}
