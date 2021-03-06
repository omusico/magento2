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
namespace Magento\Catalog\Model\Product\Attribute;

class SetManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\SetManagement
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetManagementMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeSetRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfig;

    protected function setUp()
    {
        $this->attrSetManagementMock = $this->getMock('\Magento\Eav\Api\AttributeSetManagementInterface');
        $this->attributeSetRepository = $this->getMock('\Magento\Eav\Api\AttributeSetRepositoryInterface');
        $this->eavConfig = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false);
        $this->model = new \Magento\Catalog\Model\Product\Attribute\SetManagement(
            $this->attrSetManagementMock,
            $this->attributeSetRepository,
            $this->eavConfig
        );
    }

    public function testCreate()
    {
        $skeletonId = 1;
        $attributeSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $skeletonSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');

        $this->attributeSetRepository->expects($this->once())
            ->method('get')
            ->with($skeletonId)
            ->willReturn($skeletonSetMock);

        $typeMock = $this->getMock('\Magento\Eav\Model\Entity\Type', [], [], '', false);
        $typeMock->expects($this->once())->method('getId')->willReturn(4);
        $this->eavConfig->expects($this->once())
            ->method('getEntityType')
            ->with(\Magento\Catalog\Model\Product::ENTITY)
            ->willReturn($typeMock);
        $skeletonSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(4);

        $this->attrSetManagementMock->expects($this->once())
            ->method('create')
            ->with(
                \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
                $attributeSetMock,
                $skeletonId
            )->willReturn($attributeSetMock);
        $this->assertEquals($attributeSetMock, $this->model->create($attributeSetMock, $skeletonId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Can not create attribute set based on non product attribute set.
     */
    public function testCreateNonProductAttributeSet()
    {
        $skeletonId = 1;
        $attributeSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $skeletonSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $this->attributeSetRepository->expects($this->once())
            ->method('get')
            ->with($skeletonId)
            ->willReturn($skeletonSetMock);

        $typeMock = $this->getMock('\Magento\Eav\Model\Entity\Type', [], [], '', false);
        $typeMock->expects($this->once())->method('getId')->willReturn(4);
        $this->eavConfig->expects($this->once())
            ->method('getEntityType')
            ->with(\Magento\Catalog\Model\Product::ENTITY)
            ->willReturn($typeMock);
        $skeletonSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(3);
        $this->model->create($attributeSetMock, $skeletonId);
    }
}
