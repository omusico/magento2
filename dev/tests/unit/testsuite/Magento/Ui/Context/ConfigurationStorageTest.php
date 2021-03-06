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

namespace Magento\Ui\Context;

use Magento\Framework\Data\Collection as DataCollection;

/**
 * Class ConfigurationStorageTest
 * @package Magento\Ui
 */
class ConfigurationStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $componentStorage = [];

    /**
     * @var array
     */
    protected $dataStorage = [];

    /**
     * @var array
     */
    protected $metaStorage = [];

    /**
     * @var DataCollection[]
     */
    protected $collectionStorage = [];

    /**
     * @var array
     */
    protected $cloudDataStorage = [];

    /**
     * @var ConfigurationStorage
     */
    protected $configurationStorage;

    public function setUp()
    {
        $this->configurationStorage = new ConfigurationStorage();
    }

    public function testAddGetComponentsData()
    {
        $configuration = ['key' => 'value'];
        $name = 'myName';
        $parentName = 'thisParentName';
        $configurationModel = new Configuration($name, $parentName, $configuration);
        $this->componentStorage = [$configurationModel->getName() => $configurationModel];
        $this->configurationStorage->addComponentsData($configurationModel);

        $this->assertEquals($this->componentStorage, $this->configurationStorage->getComponentsData(null));
        $this->assertEquals(null, $this->configurationStorage->getComponentsData('someKey'));
        $this->assertEquals($configurationModel, $this->configurationStorage->getComponentsData($name));
    }

    public function testRemoveComponentsData()
    {
        $configuration = ['key' => 'value'];
        $name = 'myName';
        $parentName = 'thisParentName';
        $configurationModel = new Configuration($name, $parentName, $configuration);
        $this->componentStorage = [$configurationModel->getName() => $configurationModel];
        $this->configurationStorage->addComponentsData($configurationModel);
        $this->assertEquals($configurationModel, $this->configurationStorage->getComponentsData($name));
        $this->configurationStorage->removeComponentsData($configurationModel);
        $this->assertEquals(null, $this->configurationStorage->getComponentsData($name));
    }

    public function testAddGetData()
    {
        $name = 'myName';
        $dataSource = [
            'data' => ['key' => 'value'],
            'config' => ['key' => 'value'],
        ];
        $this->configurationStorage->addDataSource($name, $dataSource);
        $this->assertEquals([$name => $dataSource], $this->configurationStorage->getDataSource(null));
        $this->assertEquals(null, $this->configurationStorage->getDataSource('someKey'));
        $this->assertEquals($dataSource, $this->configurationStorage->getDataSource($name));
    }

    public function testUpdateRemoveData()
    {
        $dataSource = [
            'data' => ['key' => 'value'],
            'config' => ['key' => 'value']
        ];
        $key = 'myKey';
        $this->configurationStorage->addDataSource($key, $dataSource);
        $this->assertEquals($dataSource, $this->configurationStorage->getDataSource($key));
        $dataSource = [
            'data' => ['key1' => 'value1'],
            'config' => ['key1' => 'value1']
        ];
        $this->configurationStorage->updateDataSource($key, $dataSource);
        $this->assertEquals($dataSource, $this->configurationStorage->getDataSource($key));
        $this->configurationStorage->removeDataSource($key);
        $this->assertEquals(null, $this->configurationStorage->getDataSource($key));
    }

    public function testAddGetMeta()
    {
        $data = ['key' => 'value'];
        $key = 'myName';
        $this->configurationStorage->addMeta($key, $data);
        $this->assertEquals([$key => $data], $this->configurationStorage->getMeta(null));
        $this->assertEquals(null, $this->configurationStorage->getMeta('someKey'));
        $this->assertEquals($data, $this->configurationStorage->getMeta($key));
    }

    public function testUpdateRemoveMeta()
    {
        $data = ['key' => 'value'];
        $key = 'myKey';
        $this->configurationStorage->addMeta($key, $data);
        $this->assertEquals($data, $this->configurationStorage->getMeta($key));
        $data = ['key1' => 'value1'];
        $this->configurationStorage->updateMeta($key, $data);
        $this->assertEquals($data, $this->configurationStorage->getMeta($key));
        $this->configurationStorage->removeMeta($key);
        $this->assertEquals(null, $this->configurationStorage->getMeta($key));
    }

    public function testAddGetDataCollection()
    {
        $key = 'myName';
        $dataCollection = $this->getMock('\Magento\Framework\Data\Collection', [], [], '', false);
        $this->configurationStorage->addDataCollection($key, $dataCollection);

        $this->assertEquals([$key => $dataCollection], $this->configurationStorage->getDataCollection(null));
        $this->assertEquals(null, $this->configurationStorage->getDataCollection('someKey'));
        $this->assertEquals($dataCollection, $this->configurationStorage->getDataCollection($key));
    }

    public function testRemoveDataCollection()
    {
        $key = 'myName';
        $dataCollection = $this->getMock('\Magento\Framework\Data\Collection', [], [], '', false);
        $update = clone $dataCollection;
        $this->configurationStorage->addDataCollection($key, $dataCollection);
        $this->assertEquals($dataCollection, $this->configurationStorage->getDataCollection($key));
        $this->configurationStorage->updateDataCollection($key, $update);
        $this->assertEquals($update, $this->configurationStorage->getDataCollection($key));
    }
}
 