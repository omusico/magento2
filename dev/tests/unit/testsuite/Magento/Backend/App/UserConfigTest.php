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
 * @copyright  Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Backend\App;

class UserConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testUserRequestCreation()
    {
        $factoryMock = $this->getMock('Magento\Backend\Model\Config\Factory', [], [], '', false);
        $responseMock = $this->getMock('Magento\Framework\App\Console\Response', [], [], '', false);
        $configMock = $this->getMock('Magento\Backend\Model\Config', [], [], '', false);

        $key = 'key';
        $value = 'value';
        $request = [$key => $value];
        $model = new UserConfig($factoryMock, $responseMock, $request);
        $factoryMock->expects($this->once())->method('create')->will($this->returnValue($configMock));
        $configMock->expects($this->once())->method('setDataByPath')->with($key, $value);
        $configMock->expects($this->once())->method('save');

        $model->launch();
    }
}
