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

/**
 * Test class for \Magento\Tax\Model\Sales\Total\Quote\Subtotal
 */
use Magento\Tax\Model\Calculation;
use Magento\TestFramework\Helper\ObjectManager;

class SubtotalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxCalculationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteDetailsBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $keyBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Tax\Model\Sales\Total\Quote\Subtotal
     */
    protected $model;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->taxConfigMock = $this->getMockBuilder('\Magento\Tax\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods(['priceIncludesTax', 'getShippingTaxClass', 'shippingPriceIncludesTax', 'discountTax'])
            ->getMock();
        $this->taxCalculationMock = $this->getMockBuilder('Magento\Tax\Api\TaxCalculationInterface')
            ->getMockForAbstractClass();
        $this->quoteDetailsBuilder = $this->getMockBuilder('\Magento\Tax\Api\Data\QuoteDetailsDataBuilder')
            ->disableOriginalConstructor()
            ->setMethods([
                'getItemBuilder', 'getAddressBuilder', 'getTaxClassKeyBuilder', 'create',
                'setBillingAddress', 'setShippingAddress', 'setCustomerTaxClassKey',
                'setItems', 'setCustomerId'
            ])->getMock();
        $this->keyBuilderMock = $this->getMock(
            'Magento\Tax\Api\Data\TaxClassKeyDataBuilder',
            ['setType', 'setValue', 'create'],
            [],
            '',
            false
        );

        $this->model = $this->objectManager->getObject(
            '\Magento\Tax\Model\Sales\Total\Quote\Subtotal',
            [
                'taxConfig' => $this->taxConfigMock,
                'taxCalculationService' => $this->taxCalculationMock,
                'quoteDetailsBuilder' => $this->quoteDetailsBuilder,
                'taxClassKeyBuilder' => $this->keyBuilderMock,
            ]
        );

        $this->addressMock = $this->getMockBuilder('\Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods([
                'getAssociatedTaxables', 'getQuote', 'getBillingAddress',
                'getRegionId', 'getAllNonNominalItems', '__wakeup',
                'getParentItem'
            ])->getMock();

        $this->quoteMock = $this->getMockBuilder('\Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeMock = $this->getMockBuilder('\Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->quoteMock->expects($this->any())->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->any())->method('getStoreId')->willReturn(111);
    }

    public function testCollectEmptyAddresses()
    {
        $this->addressMock->expects($this->once())->method('getAllNonNominalItems')->willReturn(null);
        $this->taxConfigMock->expects($this->never())->method('priceIncludesTax');
        $this->model->collect($this->addressMock);
    }

    public function testCollect()
    {
        $priceIncludesTax = true;

        $this->checkGetAddressItems();
        $this->taxConfigMock->expects($this->once())->method('priceIncludesTax')->willReturn($priceIncludesTax);
        $this->addressMock->expects($this->atLeastOnce())->method('getParentItem')->willReturnSelf();
        $taxDetailsMock = $this->getMockBuilder('\Magento\Tax\Api\Data\TaxDetailsInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->taxCalculationMock->expects($this->atLeastOnce())->method('calculateTax')->willReturn($taxDetailsMock);
        $taxDetailsMock->expects($this->atLeastOnce())->method('getItems')->willReturn([]);
        $this->model->collect($this->addressMock);
    }

    /**
     * Mock checks for $this->_getAddressItems() call
     */
    protected function checkGetAddressItems()
    {
        $customerTaxClassId = 2425;
        $this->addressMock->expects($this->atLeastOnce())
            ->method('getAllNonNominalItems')->willReturn([$this->addressMock]);

        // calls in populateAddressData()
        $this->quoteDetailsBuilder->expects($this->atLeastOnce())->method('setBillingAddress');
        $this->quoteDetailsBuilder->expects($this->atLeastOnce())->method('setShippingAddress');

        $this->addressMock->expects($this->atLeastOnce())->method('getQuote')->willReturn($this->quoteMock);
        $this->quoteMock->expects($this->atLeastOnce())
            ->method('getCustomerTaxClassId')
            ->willReturn($customerTaxClassId);
        $this->quoteMock->expects($this->atLeastOnce())->method('getBillingAddress')->willReturn($this->addressMock);

        $this->keyBuilderMock->expects($this->atLeastOnce())->method('setType');
        $this->keyBuilderMock->expects($this->atLeastOnce())->method('setValue');
        $this->keyBuilderMock->expects($this->atLeastOnce())->method('create')->willReturn('taxClassKey');

        $this->quoteDetailsBuilder->expects($this->atLeastOnce())
            ->method('setCustomerTaxClassKey')
            ->with('taxClassKey');
        $this->quoteDetailsBuilder->expects($this->atLeastOnce())->method('setItems')->with([]);
        $this->quoteDetailsBuilder->expects($this->atLeastOnce())->method('setCustomerId');
        $quoteDetailsMock = $this->getMockBuilder('\Magento\Tax\Api\Data\QuoteDetailsInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->quoteDetailsBuilder->expects($this->atLeastOnce())->method('create')->willReturn($quoteDetailsMock);
    }
}
