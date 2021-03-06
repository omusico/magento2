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
namespace Magento\Ui\Component\Control;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\Escaper;

class ButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Button
     */
    protected $button;

    /**
     * @var Context| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var UrlInterface| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * Escaper
     *
     * @var Escaper| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    public function setUp()
    {
        $this->contextMock = $this->getMock(
            '\Magento\Framework\View\Element\Template\Context',
            ['getPageLayout', 'getUrlBuilder', 'getEscaper'],
            [],
            '',
            false
        );
        $this->urlBuilderMock = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $this->contextMock->expects($this->any())->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->escaperMock = $this->getMock('Magento\Framework\Escaper', ['escapeHtml'], [], '', false);
        $this->contextMock->expects($this->any())->method('getEscaper')->willReturn($this->escaperMock);
        $this->button = new Button($this->contextMock);
    }

    public function testGetType()
    {
        $this->assertEquals('button', $this->button->getType());
    }

    public function testGetAttributesHtml()
    {
        $expected = 'type="button" class="action- scalable classValue disabled" ' .
            'disabled="disabled" data-attributeKey="attributeValue" ';
        $this->button->setDisabled(true);
        $this->button->setData('url', 'url2');
        $this->button->setData('class', 'classValue');
        $this->button->setDataAttribute(['attributeKey' => 'attributeValue']);
        $this->escaperMock->expects($this->any())->method('escapeHtml')->withAnyParameters()->willReturnArgument(0);
        $this->assertEquals($expected, $this->button->getAttributesHtml());
    }
}
