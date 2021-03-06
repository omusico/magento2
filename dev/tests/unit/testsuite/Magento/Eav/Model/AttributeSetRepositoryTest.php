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
namespace Magento\Eav\Model;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AttributeSetRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeSetRepository
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $setFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $eavConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resultBuilderMock;

    protected function setUp()
    {
        $this->resourceMock = $this->getMock(
            'Magento\Eav\Model\Resource\Entity\Attribute\Set',
            array(),
            array(),
            '',
            false
        );
        $this->setFactoryMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\SetFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->collectionFactoryMock = $this->getMock(
            'Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array('getEntityType'), array(), '', false);
        $this->resultBuilderMock = $this->getMock(
            '\Magento\Eav\Api\Data\AttributeSetSearchResultsDataBuilder',
            array('setSearchCriteria', 'setItems', 'setTotalCount', 'create', '__wakeup'),
            array(),
            '',
            false
        );
        $this->model = new AttributeSetRepository(
            $this->resourceMock,
            $this->setFactoryMock,
            $this->collectionFactoryMock,
            $this->eavConfigMock,
            $this->resultBuilderMock
        );
    }

    public function testGet()
    {
        $attributeSetId = 1;
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $this->resourceMock->expects($this->once())->method('load')->with($attributeSetMock, $attributeSetId, null);
        $attributeSetMock->expects($this->any())->method('getId')->will($this->returnValue($attributeSetId));
        $this->assertEquals($attributeSetMock, $this->model->get($attributeSetId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 9999
     */
    public function testGetThrowsExceptionIfRequestedAttributeSetDoesNotExist()
    {
        $attributeSetId = 9999;
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $this->resourceMock->expects($this->once())->method('load')->with($attributeSetMock, $attributeSetId, null);
        $this->model->get($attributeSetId);
    }

    public function testSave()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('save')->with($attributeSetMock);
        $this->assertEquals($attributeSetMock, $this->model->save($attributeSetMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage There was an error saving attribute set.
     */
    public function testSaveThrowsExceptionIfGivenEntityCannotBeSaved()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('save')->with($attributeSetMock)->willThrowException(
            new \Exception('Some internal exception message.')
        );
        $this->model->save($attributeSetMock);
    }

    public function testDelete()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('delete')->with($attributeSetMock);
        $this->assertTrue($this->model->delete($attributeSetMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage There was an error deleting attribute set.
     */
    public function testDeleteThrowsExceptionIfGivenEntityCannotBeDeleted()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('delete')->with($attributeSetMock)->willThrowException(
            new \Magento\Framework\Exception\CouldNotDeleteException('Some internal exception message.')
        );
        $this->model->delete($attributeSetMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Default attribute set can not be deleted
     */
    public function testDeleteThrowsExceptionIfGivenAttributeSetIsDefault()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('delete')->with($attributeSetMock)->willThrowException(
            new \Magento\Framework\Exception\StateException('Some internal exception message.')
        );
        $this->model->delete($attributeSetMock);
    }

    public function testDeleteById()
    {
        $attributeSetId = 1;
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $attributeSetMock->expects($this->any())->method('getId')->will($this->returnValue($attributeSetId));
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $this->resourceMock->expects($this->once())->method('load')->with($attributeSetMock, $attributeSetId, null);
        $this->resourceMock->expects($this->once())->method('delete')->with($attributeSetMock);
        $this->assertTrue($this->model->deleteById($attributeSetId));
    }

    public function testGetList()
    {
        $entityTypeCode = 'entity_type_code_value';
        $entityTypeId = 41;

        $searchCriteriaMock = $this->getMock('\Magento\Framework\Api\SearchCriteriaInterface');

        $filterGroupMock = $this->getMock('\Magento\Framework\Api\Search\FilterGroup', [], [], '', false);
        $searchCriteriaMock->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroupMock]);

        $filterMock = $this->getMock('\Magento\Framework\Api\Filter', [], [], '', false);
        $filterGroupMock->expects($this->once())->method('getFilters')->willReturn([$filterMock]);

        $filterMock->expects($this->once())->method('getField')->willReturn('entity_type_code');
        $filterMock->expects($this->once())->method('getValue')->willReturn($entityTypeCode);

        $collectionMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection',
            ['setEntityTypeFilter', 'setCurPage', 'setPageSize', 'getItems', 'getSize'],
            [],
            '',
            false
        );

        $entityTypeMock = $this->getMock('\Magento\Eav\Model\Entity\Type', [], [], '', false);
        $entityTypeMock->expects($this->once())->method('getId')->willReturn($entityTypeId);
        $this->eavConfigMock->expects($this->once())
            ->method('getEntityType')
            ->with($entityTypeCode)
            ->willReturn($entityTypeMock);

        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($collectionMock);
        $collectionMock->expects($this->once())
            ->method('setEntityTypeFilter')
            ->with($entityTypeId)
            ->willReturnSelf();

        $searchCriteriaMock->expects($this->once())->method('getCurrentPage')->willReturn(1);
        $searchCriteriaMock->expects($this->once())->method('getPageSize')->willReturn(10);

        $collectionMock->expects($this->once())->method('setCurPage')->with(1)->willReturnSelf();
        $collectionMock->expects($this->once())->method('setPageSize')->with(10)->willReturnSelf();

        $attributeSetMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $collectionMock->expects($this->once())->method('getItems')->willReturn([$attributeSetMock]);
        $collectionMock->expects($this->once())->method('getSize')->willReturn(1);

        $this->resultBuilderMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->resultBuilderMock->expects($this->once())
            ->method('setItems')
            ->with([$attributeSetMock])
            ->willReturnSelf();
        $this->resultBuilderMock->expects($this->once())->method('setTotalCount')->with(1)->willReturnSelf();

        $resultMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetSearchResultsInterface', [], [], '', false);
        $this->resultBuilderMock->expects($this->once())->method('create')->willReturn($resultMock);

        $this->model->getList($searchCriteriaMock);
    }
}
