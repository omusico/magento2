<?php
/**
 * Find "backend/system.xml" files and validate them
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
namespace Magento\Test\Integrity\Magento\Backend;

class SystemConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $configFile
             */
            function ($configFile) {
                $dom = new \DOMDocument();
                $dom->loadXML(file_get_contents($configFile));
                $schema = \Magento\Framework\Test\Utility\Files::init()->getPathToSource() .
                    '/app/code/Magento/Backend/etc/system_file.xsd';
                $errors = \Magento\Framework\Config\Dom::validateDomDocument($dom, $schema);
                if ($errors) {
                    $this->fail('XML-file has validation errors:' . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
                }
            },
            \Magento\Framework\Test\Utility\Files::init()->getConfigFiles('adminhtml/system.xml', array())
        );
    }
}
