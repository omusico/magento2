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
 
namespace Magento\Catalog\Model\Category;

class AttributeRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeRepository
     */
    protected $model;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultMock;
    
    protected function setUp()
    {
        $this->searchBuilderMock =
            $this->getMock('Magento\Framework\Api\SearchCriteriaDataBuilder', [], [], '', false);
        $this->filterBuilderMock =
            $this->getMock('Magento\Framework\Api\FilterBuilder', [], [], '', false);
        $this->attributeRepositoryMock =
            $this->getMock('Magento\Eav\Api\AttributeRepositoryInterface', [], [], '', false);
        $this->metadataConfigMock =
            $this->getMock('Magento\Framework\Api\Config\MetadataConfig', [], [], '', false);
        $this->searchResultMock =
            $this->getMock(
                'Magento\Framework\Api\SearchResultsInterface',
                [
                    'getItems',
                    'getSearchCriteria',
                    'getTotalCount',
                    '__wakeup'
                ],
                [],
                '',
                false);
        $this->model = new AttributeRepository(
            $this->metadataConfigMock,
            $this->searchBuilderMock,
            $this->filterBuilderMock,
            $this->attributeRepositoryMock
        );
    }

    public function testGetList()
    {
        $searchCriteriaMock = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $this->attributeRepositoryMock->expects($this->once())
            ->method('getList')
            ->with(\Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE, $searchCriteriaMock)
            ->willReturn($this->searchResultMock);

        $this->model->getList($searchCriteriaMock);
    }

    public function testGet()
    {
        $attributeCode = 'some Attribute Code';
        $dataInterfaceMock =
            $this->getMock('Magento\Catalog\Api\Data\CategoryAttributeInterface', [], [], '', false);
        $this->attributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE, $attributeCode)
            ->willReturn($dataInterfaceMock);

        $this->model->get($attributeCode);
    }

    public function testGetCustomAttributesMetadata()
    {
        $filterMock = $this->getMock('Magento\Framework\Service\V1\Data\Filter', [], [], '', false);
        $this->filterBuilderMock->expects($this->once())->method('setField')
            ->with('attribute_set_id')->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('setValue')->with(
            \Magento\Catalog\Api\Data\CategoryAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID
        )->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('create')->willReturn($filterMock);
        $this->searchBuilderMock->expects($this->once())->method('addFilter')->with([$filterMock])->willReturnSelf();
        $searchCriteriaMock = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $this->searchBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteriaMock);
        $itemMock = $this->getMock('Magento\Framework\Object', [], [], '', false);
        $this->attributeRepositoryMock->expects($this->once())->method('getList')->with(
            \Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteriaMock
        )->willReturn($this->searchResultMock);
        $this->searchResultMock->expects($this->once())->method('getItems')->willReturn([$itemMock]);
        $this->metadataConfigMock->expects($this->once())
            ->method('getCustomAttributesMetadata')->with(null)->willReturn(['attribute']);
        $expected = array_merge([$itemMock], ['attribute']);

        $this->assertEquals($expected, $this->model->getCustomAttributesMetadata(null));
    }
}
