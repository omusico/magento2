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

namespace Magento\Catalog\Model;

class CategoryLinkRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\CategoryLinkRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productLinkMock;

    protected function setUp()
    {
        $this->categoryRepositoryMock = $this->getMock('\Magento\Catalog\Api\CategoryRepositoryInterface');
        $this->productRepositoryMock = $this->getMock('\Magento\Catalog\Api\ProductRepositoryInterface');
        $this->productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\CategoryProductLinkInterface');
        $this->model = new \Magento\Catalog\Model\CategoryLinkRepository(
            $this->categoryRepositoryMock,
            $this->productRepositoryMock
        );
    }

    public function testSave()
    {
        $categoryId = 42;
        $productId = 55;
        $productPosition = 1;
        $sku = 'testSku';
        $productPositions = [$productId => $productPosition];
        $categoryMock = $this->getMock(
            '\Magento\Catalog\Model\Category',
            ['getPostedProducts', 'getProductsPosition', 'setPostedProducts', 'save'],
            [],
            '',
            false
        );
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [],
            [],
            '',
            false
        );
        $this->productLinkMock->expects($this->once())->method('getCategoryId')->willReturn($categoryId);
        $this->productLinkMock->expects($this->once())->method('getSku')->willReturn($sku);
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($sku)->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn([]);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $this->productLinkMock->expects($this->once())->method('getPosition')->willReturn($productPosition);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with($productPositions);
        $categoryMock->expects($this->once())->method('save');
        $this->assertTrue($this->model->save($this->productLinkMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not save product "55" with position 1 to category 42
     */
    public function testSaveWithCouldNotSaveException()
    {
        $categoryId = 42;
        $productId = 55;
        $productPosition = 1;
        $sku = 'testSku';
        $productPositions = [$productId => $productPosition];
        $categoryMock = $this->getMock(
            '\Magento\Catalog\Model\Category',
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId'],
            [],
            '',
            false
        );
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [],
            [],
            '',
            false
        );
        $this->productLinkMock->expects($this->once())->method('getCategoryId')->willReturn($categoryId);
        $this->productLinkMock->expects($this->once())->method('getSku')->willReturn($sku);
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($sku)->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn([]);
        $productMock->expects($this->exactly(2))->method('getId')->willReturn($productId);
        $this->productLinkMock->expects($this->exactly(2))->method('getPosition')->willReturn($productPosition);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with($productPositions);
        $categoryMock->expects($this->once())->method('getId')->willReturn($categoryId);
        $categoryMock->expects($this->once())->method('save')->willThrowException(new \Exception());
        $this->model->save($this->productLinkMock);
    }

    public function testDeleteByIds()
    {
        $categoryId = "42";
        $productSku = "testSku";
        $productId = 55;
        $productPositions = [55 => 1];
        $categoryMock = $this->getMock(
            '\Magento\Catalog\Model\Category',
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId'],
            [],
            '',
            false
        );
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [],
            [],
            '',
            false
        );
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($productPositions);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with([]);
        $categoryMock->expects($this->once())->method('save');
        $this->assertTrue($this->model->deleteByIds($categoryId, $productSku));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not save product "55" with position %position to category 42
     */
    public function testDeleteByIdsWithCouldNotSaveException()
    {
        $categoryId = "42";
        $productSku = "testSku";
        $productId = 55;
        $productPositions = [55 => 1];
        $categoryMock = $this->getMock(
            '\Magento\Catalog\Model\Category',
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId'],
            [],
            '',
            false
        );
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [],
            [],
            '',
            false
        );
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($productPositions);
        $productMock->expects($this->exactly(2))->method('getId')->willReturn($productId);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with([]);
        $categoryMock->expects($this->once())->method('getId')->willReturn($categoryId);
        $categoryMock->expects($this->once())->method('save')->willThrowException(new \Exception());
        $this->model->deleteByIds($categoryId, $productSku);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Category does not contain specified product
     */
    public function testDeleteWithInputException()
    {
        $categoryId = "42";
        $productSku = "testSku";
        $productId = 60;
        $productPositions = [55 => 1];
        $this->productLinkMock->expects($this->once())->method('getCategoryId')->willReturn($categoryId);
        $this->productLinkMock->expects($this->once())->method('getSku')->willReturn($productSku);
        $categoryMock = $this->getMock(
            '\Magento\Catalog\Model\Category',
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId'],
            [],
            '',
            false
        );
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [],
            [],
            '',
            false
        );
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($productPositions);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);

        $categoryMock->expects($this->never())->method('save');
        $this->assertTrue($this->model->delete($this->productLinkMock));
    }

    public function testDelete()
    {
        $categoryId = "42";
        $productSku = "testSku";
        $productId = 55;
        $productPositions = [55 => 1];
        $this->productLinkMock->expects($this->once())->method('getCategoryId')->willReturn($categoryId);
        $this->productLinkMock->expects($this->once())->method('getSku')->willReturn($productSku);
        $categoryMock = $this->getMock(
            '\Magento\Catalog\Model\Category',
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId'],
            [],
            '',
            false
        );
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [],
            [],
            '',
            false
        );
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($productPositions);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with([]);
        $categoryMock->expects($this->once())->method('save');
        $this->assertTrue($this->model->delete($this->productLinkMock));
    }
}
