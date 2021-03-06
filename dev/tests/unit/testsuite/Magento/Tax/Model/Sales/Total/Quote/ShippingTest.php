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
namespace Magento\Tax\Model\Sales\Total\Quote;

class ShippingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $taxConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $taxCalculationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteDetailsBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $itemDetailsBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $taxClassKeyBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $addressBuilderMock;

    /**
     * @var Shipping
     */
    private $model;

    protected function setUp()
    {
        $this->taxConfigMock = $this->getMock('Magento\Tax\Model\Config', [], [], '', false);
        $this->taxCalculationMock = $this->getMock('Magento\Tax\Api\TaxCalculationInterface');
        $this->quoteDetailsBuilder = $this->getMock('Magento\Tax\Api\Data\QuoteDetailsDataBuilder',
            ['create'],
            [],
            '',
            false
        );
        $this->itemDetailsBuilder = $this->getMock('Magento\Tax\Api\Data\QuoteDetailsItemDataBuilder',
            [
                'setType',
                'setCode',
                'setQuantity',
                'setUnitPrice',
                'setDiscountAmount',
                'setTaxClassKey',
                'setTaxIncluded',
                'create',
            ],
            [],
            '',
            false
        );
        $this->taxClassKeyBuilder= $this->getMock('Magento\Tax\Api\Data\TaxClassKeyDataBuilder',
            ['setType', 'setValue', 'create'],
            [],
            '',
            false
        );
        $this->addressBuilderMock = $this->getMock('Magento\Customer\Service\V1\Data\AddressBuilder',
            [],
            [],
            '',
            false
        );

        $this->model = new Shipping(
            $this->taxConfigMock,
            $this->taxCalculationMock,
            $this->quoteDetailsBuilder,
            $this->itemDetailsBuilder,
            $this->taxClassKeyBuilder,
            $this->addressBuilderMock
        );
    }
    public function testCollectDoesNotCalculateTaxIfThereIsNoItemsRelatedToGivenAddress()
    {
        $storeId = 1;
        $storeMock = $this->getMockObject('Magento\Store\Model\Store', array(
            'store_id' => $storeId,
        ));
        $quoteMock = $this->getMockObject(
            'Magento\Sales\Model\Quote',
            array(
                'store' => $storeMock,
            )
        );
        $addressMock = $this->getMockObject('Magento\Sales\Model\Quote\Address', array(
            'all_non_nominal_items' => array(),
            'shipping_tax_calculation_amount' => 100,
            'base_shipping_tax_calculation_amount' => 200,
            'shipping_discount_amount' => 10,
            'base_shipping_discount_amount' => 20,
            'quote' => $quoteMock,
        ));
        $this->taxCalculationMock->expects($this->never())->method('calculateTax');
        $this->model->collect($addressMock);
    }

    public function testCollect()
    {
        $this->markTestIncomplete('Target code is not unit testable. Refactoring is required.');
    }

    /**
     * Retrieve mock object with mocked getters
     *
     * @param $className
     * @param array $objectState
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockObject($className, array $objectState)
    {
        $getterValueMap = [];
        $methods = ['__wakeup'];
        foreach ($objectState as $key => $value) {
            $getterName = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $getterValueMap[$getterName] = $value;
            $methods[] = $getterName;
        }

        $mock = $this->getMock($className, $methods, [], '', false);
        foreach ($getterValueMap as $getterName => $value) {
            $mock->expects($this->any())->method($getterName)->will($this->returnValue($value));
        }

        return $mock;
    }
}
