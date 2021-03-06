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

namespace Magento\Ui\DataProvider;

use Magento\Framework\ObjectManagerInterface;
use Magento\Ui\DataProvider\Config\Data as Config;

/**
 * Class Manager
 */
class Manager
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $metadataFactory;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(Config $config, ObjectManagerInterface $objectManager, MetadataFactory $metadataFactory)
    {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Returns Data Source metadata
     *
     * @param string $dataSource
     * @return \Magento\Ui\DataProvider\Metadata
     */
    public function getMetadata($dataSource)
    {
        return $this->metadataFactory->create(
            [
                'config' => $this->config->getDataSource($dataSource)
            ]
        );
    }

    /**
     * @param string $dataSource
     * @param array $filters
     * @return mixed
     */
    public function getCollectionData($dataSource, array $filters = [])
    {
        $collectionHash = md5($dataSource . serialize($filters));
        if (!isset($this->cache[$collectionHash])) {
            $config = $this->config->getDataSource($dataSource);
            /** @var \Magento\Framework\Data\Collection\Db $collection */
            $collection = $this->objectManager->create($config['dataSet']);

            foreach ($config['fields'] as $field) {
                if (isset($field['source']) && $field['source'] == 'eav') {
                    $collection->addAttributeToSelect($field['name']);
                }
            }

            if ($filters) {
                foreach ($filters as $field => $expression) {
                    $collection->addFieldToFilter($field, $expression);
                }
            }
            $this->cache[$collectionHash] = $collection->getItems();
        }
        return $this->cache[$collectionHash];
    }

    /**
     * Returns data by specified Data Source name
     *
     * @param string $dataSource
     * @param array $filters
     * @return array
     */
    public function getData($dataSource, array $filters = [])
    {
        $children = $this->getMetadata($dataSource)->getChildren();
        $fields = $this->getMetadata($dataSource)->getFields();
        $items = $this->getCollectionData($dataSource, $filters);

        $rows = [];
        foreach ($items as $item) {
            $row = [];
            foreach ($fields as $name => $field) {
                if (isset($field['source']) && $field['source'] == 'lookup') {
                    $lookupCollection = $this->getCollectionData(
                        $field['reference']['target'],
                        [$field['reference']['targetField'] => $item->getData($field['reference']['referencedField'])]
                    );
                    $lookup = reset($lookupCollection);
                    $row[$name] = $lookup[$field['reference']['neededField']];
                } elseif (isset($field['source']) && $field['source'] == 'reference') {
                    $lookupCollection = $this->getCollectionData(
                        $field['reference']['target'],
                        [$field['reference']['targetField'] => $item->getData($field['reference']['referencedField'])]
                    );
                    $lookup = reset($lookupCollection);
                    $isReferenced = isset($lookup[$field['reference']['neededField']])
                        && $lookup[$field['reference']['neededField']] == $item->getId();
                    $row[$name] = $isReferenced;
                } elseif (isset($field['source']) && $field['source'] == 'option') {
                    $row[$name] = $item->getData($field['reference']['referencedField']);
                } else {
                    $row[$name] = $item->getData($name);
                }

                if (isset($field['size'])) {
                    $row[$name] = explode("\n", $row[$name]);
                }
            }
            if (!empty($children)) {
                foreach ($children as $name => $reference) {
                    $filter = [];
                    foreach ($reference as $metadata) {
                        $filter[$metadata['referencedField']] = $row[$metadata['targetField']];
                    }
                    $row[$name] = $this->getData($name, $filter);
                    if (empty($row[$name])) {
                        unset($row[$name]);
                    }
                }
            }
            $rows[$item->getId()] = $row;
        }
        return $rows;
    }
}
