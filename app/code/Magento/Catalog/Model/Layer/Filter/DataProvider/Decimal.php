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
namespace Magento\Catalog\Model\Layer\Filter\DataProvider;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class Decimal
{
    const MIN_RANGE_POWER = 10;

    /**
     * @var int
     */
    private $max;

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $range;

    /**
     * @var array
     */
    private $rangeItemsCount = [];

    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\Decimal
     */
    private $resource;

    /**
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\Decimal $resource
     */
    public function __construct(\Magento\Catalog\Model\Resource\Layer\Filter\Decimal $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param FilterInterface $filter
     * @return int
     */
    public function getRange(FilterInterface $filter)
    {
        $range = $this->range;
        if (!$range) {
            $maxValue = $this->getMaxValue($filter);
            $index = 1;
            do {
                $range = pow(10, strlen(floor($maxValue)) - $index);
                $items = $this->getRangeItemCounts($range, $filter);
                $index++;
            } while ($range > self::MIN_RANGE_POWER && count($items) < 2);
            $this->range = $range;
        }

        return $range;
    }

    /**
     * @param int $range
     * @return void
     */
    public function setRange($range)
    {
        $this->range = $range;
    }

    /**
     * Retrieve maximum value from layer products set
     *
     * @param FilterInterface $filter
     * @return float
     */
    public function getMaxValue(FilterInterface $filter)
    {
        if (is_null($this->max)) {
            $this->loadValues($filter);
        }

        return $this->max;
    }

    /**
     * Retrieve minimal value from layer products set
     *
     * @param FilterInterface $filter
     * @return float
     */
    public function getMinValue(FilterInterface $filter)
    {
        if (is_null($this->min)) {
            $this->loadValues($filter);
        }

        return $this->min;
    }

    /**
     * Retrieve information about products count in range
     *
     * @param int $range
     * @param FilterInterface $filter
     * @return mixed
     */
    public function getRangeItemCounts($range, FilterInterface $filter)
    {
        $count = array_key_exists($range, $this->rangeItemsCount) ? $this->rangeItemsCount[$range] : null;
        if (is_null($count)) {
            $count = $this->getResource()
                ->getCount($filter, $range);
            $this->rangeItemsCount[$range] = $count;
        }

        return $count;
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Layer\Filter\Decimal
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param FilterInterface $filter
     * @return mixed
     */
    private function loadValues(FilterInterface $filter)
    {
        list($min, $max) = $this->getResource()
            ->getMinMax($filter);
        $this->min = $min;
        $this->max = $max;

        return $this;
    }
}
