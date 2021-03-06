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

namespace Magento\Ui\Component;

use Magento\Ui\Component\Control\ActionPool;
use Magento\Ui\Context\ConfigurationFactory;
use Magento\Ui\Component\Listing\RowPool;
use Magento\Ui\Component\Listing\OptionsFactory;
use Magento\Framework\View\Element\Template\Context;

class ListingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionPool;

    /**
     * @var OptionsFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionsFactory;

    /**
     * @var RowPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rowPool;

    /**
     * @var Context
     */
    protected $templateContext;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\View\Element\UiComponent\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationFactory;

    /**
     * @var \Magento\Framework\View\Element\UiComponent\Context |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $renderContext;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Ui\Context\Configuration |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    /**
     * @var \Magento\Ui\Context\ConfigurationStorage |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configStorage;

    /**
     * @var \Magento\Ui\ContentType\ContentTypeFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeFactory;

    /**
     * @var Listing
     */
    protected $listingView;

    public function setUp()
    {
        $this->objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->actionPool = $this->getMock('\Magento\Ui\Component\Control\ActionPool', [], [], '', false);
        $this->optionsFactory = $this->getMock('\Magento\Ui\Component\Listing\OptionsFactory', [], [], '', false);
        $this->rowPool = $this->getMock('\Magento\Ui\Component\Listing\RowPool', [], [], '', false);
        $this->renderContext = $this->getMock('\Magento\Framework\View\Element\UiComponent\Context', [], [], '', false);
        $this->templateContext = $this->getMock(
            'Magento\Framework\View\Element\Template\Context',
            [],
            [],
            '',
            false
        );
        $this->configStorage = $this->getMock(
            'Magento\Ui\Context\ConfigurationStorage',
            [],
            [],
            '',
            false
        );
        $this->localeDate = $this->getMock('Magento\Framework\Stdlib\DateTime\Timezone', [], [], '', false);
        $this->urlBuilder = $this->getMock('Magento\Backend\Model\Url', ['getUrl'], [], '', false);
        $this->templateContext->expects($this->once())
            ->method('getLocaleDate')
            ->willReturn($this->localeDate);
        $this->templateContext->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilder);
        $this->contentTypeFactory = $this->getMock('Magento\Ui\ContentType\ContentTypeFactory', [], [], '', false);
        $this->configurationFactory = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\ConfigFactory',
            [],
            [],
            '',
            false
        );
        $configurationBuilder = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface',
            ['toJson'],
            [],
            '',
            false
        );
        $this->configuration = $this->getMock('Magento\Ui\Context\Configuration', [], [], '', false);
        $this->renderContext->expects($this->any())
            ->method('getStorage')
            ->willReturn($this->configStorage);

        $this->listingView = $this->objectManagerHelper->getObject(
            '\Magento\Ui\Component\Listing',
            [
                'context' => $this->templateContext,
                'renderContext' => $this->renderContext,
                'contentTypeFactory' => $this->contentTypeFactory,
                'configFactory' => $this->configurationFactory,
                'configBuilder' => $configurationBuilder,
                'optionsFactory' => $this->optionsFactory,
                'actionPool' => $this->actionPool,
                'dataProviderRowPool' => $this->rowPool
            ]
        );
    }

    protected function prepareMeta()
    {
        $meta = ['fields' => [['data_type' => 'date', 'options_provider' => 'provider', 'options' => ['option1']]]];
        $this->listingView->setData('meta', $meta);
        $this->localeDate->expects($this->any())
            ->method('getDateTimeFormat')
            ->with('medium')
            ->willReturn('format_type');
        $options = $this->getMock('Magento\Cms\Ui\DataProvider\Page\Options\PageLayout', [], [], '', false);
        $this->optionsFactory->expects($this->any())
            ->method('create')
            ->with('provider')
            ->willReturn($options);
        $options->expects($this->any())
            ->method('getOptions')
            ->with(['option1'])
            ->willReturn(['option1']);
    }

    public function testPrepare()
    {
        $this->prepareMeta();
        $config = [
            'page_actions' => [
                'add' => [
                    'name' => 'add',
                    'label' => __('Add New'),
                    'class' => 'primary',
                    'url' => 'http://some.url'
                ]
            ]
        ];

        $this->urlBuilder->expects($this->at(0))
            ->method('getUrl')
            ->with('*/*/new')
            ->willReturn('http://mage.local/category/new');
        $dataCollection = $this->getMock('Magento\Framework\Data\Collection', [], [], '', false);
        $this->listingView->setData('configuration', $config);
        $this->listingView->setData('name', 'someName');
        $this->listingView->setData('dataSource', $dataCollection);
        $this->actionPool->expects($this->once())
            ->method('add')
            ->with('add', $config['page_actions']['add'], $this->listingView);

        $this->configurationFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->configuration);

        $this->assertNull($this->listingView->prepare());
    }
}
