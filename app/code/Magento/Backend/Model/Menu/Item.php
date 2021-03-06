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
namespace Magento\Backend\Model\Menu;

/**
 * Menu item. Should be used to create nested menu structures with \Magento\Backend\Model\Menu
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Item
{
    /**
     * Menu item id
     *
     * @var string
     */
    protected $_id;

    /**
     * Menu item title
     *
     * @var string
     */
    protected $_title;

    /**
     * Module of menu item
     *
     * @var string
     */
    protected $_moduleName;

    /**
     * Menu item sort index in list
     *
     * @var string
     */
    protected $_sortIndex = null;

    /**
     * Menu item action
     *
     * @var string
     */
    protected $_action = null;

    /**
     * Parent menu item id
     *
     * @var string
     */
    protected $_parentId = null;

    /**
     * Acl resource of menu item
     *
     * @var string
     */
    protected $_resource;

    /**
     * Item tooltip text
     *
     * @var string
     */
    protected $_tooltip;

    /**
     * Path from root element in tree
     *
     * @var string
     */
    protected $_path = '';

    /**
     * Acl
     *
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_acl;

    /**
     * Module that item is dependent on
     *
     * @var string|null
     */
    protected $_dependsOnModule;

    /**
     * Global config option that item is dependent on
     *
     * @var string|null
     */
    protected $_dependsOnConfig;

    /**
     * Submenu item list
     *
     * @var \Magento\Backend\Model\Menu
     */
    protected $_submenu;

    /**
     * @var \Magento\Backend\Model\MenuFactory
     */
    protected $_menuFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_urlModel;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Backend\Model\Menu\Item\Validator
     */
    protected $_validator;

    /**
     * Serialized submenu string
     *
     * @var string
     */
    protected $_serializedSubmenu;

    /**
     * Module list
     *
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $_moduleManager;

    /**
     * @param Item\Validator $validator
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\MenuFactory $menuFactory
     * @param \Magento\Backend\Model\UrlInterface $urlModel
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Item\Validator $validator,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\MenuFactory $menuFactory,
        \Magento\Backend\Model\UrlInterface $urlModel,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = array()
    ) {
        $this->_validator = $validator;
        $this->_validator->validate($data);

        $this->_moduleManager = $moduleManager;
        $this->_acl = $authorization;
        $this->_scopeConfig = $scopeConfig;
        $this->_menuFactory = $menuFactory;
        $this->_urlModel = $urlModel;
        $this->_moduleName = isset($data['module']) ? $data['module'] : 'Magento_Backend';
        $this->_moduleList = $moduleList;

        $this->_id = $data['id'];
        $this->_title = $data['title'];
        $this->_action = $this->_getArgument($data, 'action');
        $this->_resource = $this->_getArgument($data, 'resource');
        $this->_dependsOnModule = $this->_getArgument($data, 'dependsOnModule');
        $this->_dependsOnConfig = $this->_getArgument($data, 'dependsOnConfig');
        $this->_tooltip = $this->_getArgument($data, 'toolTip', '');
    }

    /**
     * Retrieve argument element, or default value
     *
     * @param array $array
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getArgument(array $array, $key, $defaultValue = null)
    {
        return isset($array[$key]) ? $array[$key] : $defaultValue;
    }

    /**
     * Retrieve item id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Check whether item has subnodes
     *
     * @return bool
     */
    public function hasChildren()
    {
        return !is_null($this->_submenu) && (bool)$this->_submenu->count();
    }

    /**
     * Retrieve submenu
     *
     * @return \Magento\Backend\Model\Menu
     */
    public function getChildren()
    {
        if (!$this->_submenu) {
            $this->_submenu = $this->_menuFactory->create();
        }
        return $this->_submenu;
    }

    /**
     * Retrieve menu item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ((bool)$this->_action) {
            return $this->_urlModel->getUrl((string)$this->_action, array('_cache_secret_key' => true));
        }
        return '#';
    }

    /**
     * Retrieve menu item action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Set Item action
     *
     * @param string $action
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setAction($action)
    {
        $this->_validator->validateParam('action', $action);
        $this->_action = $action;
        return $this;
    }

    /**
     * Check whether item has javascript callback on click
     *
     * @return bool
     */
    public function hasClickCallback()
    {
        return $this->getUrl() == '#';
    }

    /**
     * Retrieve item click callback
     *
     * @return string
     */
    public function getClickCallback()
    {
        if ($this->getUrl() == '#') {
            return 'return false;';
        }
        return '';
    }

    /**
     * Retrieve tooltip text title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set Item title
     *
     * @param string $title
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setTitle($title)
    {
        $this->_validator->validateParam('title', $title);
        $this->_title = $title;
        return $this;
    }

    /**
     * Check whether item has tooltip text
     *
     * @return bool
     */
    public function hasTooltip()
    {
        return (bool)$this->_tooltip;
    }

    /**
     * Retrieve item tooltip text
     *
     * @return string
     */
    public function getTooltip()
    {
        return $this->_tooltip;
    }

    /**
     * Set Item tooltip
     *
     * @param string $tooltip
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setTooltip($tooltip)
    {
        $this->_validator->validateParam('toolTip', $tooltip);
        $this->_tooltip = $tooltip;
        return $this;
    }

    /**
     * Set Item module
     *
     * @param string $module
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setModule($module)
    {
        $this->_validator->validateParam('module', $module);
        $this->_moduleName = $module;
        return $this;
    }

    /**
     * Set Item module dependency
     *
     * @param string $moduleName
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setModuleDependency($moduleName)
    {
        $this->_validator->validateParam('dependsOnModule', $moduleName);
        $this->_dependsOnModule = $moduleName;
        return $this;
    }

    /**
     * Set Item config dependency
     *
     * @param string $configPath
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setConfigDependency($configPath)
    {
        $this->_validator->validateParam('dependsOnConfig', $configPath);
        $this->_dependsOnConfig = $configPath;
        return $this;
    }

    /**
     * Check whether item is disabled. Disabled items are not shown to user
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->_moduleManager->isOutputEnabled(
            $this->_moduleName
        ) || !$this->_isModuleDependenciesAvailable() || !$this->_isConfigDependenciesAvailable();
    }

    /**
     * Check whether module that item depends on is active
     *
     * @return bool
     */
    protected function _isModuleDependenciesAvailable()
    {
        if ($this->_dependsOnModule) {
            $module = $this->_dependsOnModule;
            return $this->_moduleList->has($module);
        }
        return true;
    }

    /**
     * Check whether config dependency is available
     *
     * @return bool
     */
    protected function _isConfigDependenciesAvailable()
    {
        if ($this->_dependsOnConfig) {
            return $this->_scopeConfig->isSetFlag((string)$this->_dependsOnConfig, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        return true;
    }

    /**
     * Check whether item is allowed to the user
     *
     * @return bool
     */
    public function isAllowed()
    {
        try {
            return $this->_acl->isAllowed((string)$this->_resource);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        if ($this->_submenu) {
            $this->_serializedSubmenu = $this->_submenu->serialize();
        }
        return array(
            '_parentId',
            '_moduleName',
            '_sortIndex',
            '_dependsOnConfig',
            '_id',
            '_resource',
            '_path',
            '_action',
            '_dependsOnModule',
            '_tooltip',
            '_title',
            '_serializedSubmenu'
        );
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_moduleManager = $objectManager->get('Magento\Framework\Module\Manager');
        $this->_validator = $objectManager->get('Magento\Backend\Model\Menu\Item\Validator');
        $this->_acl = $objectManager->get('Magento\Framework\AuthorizationInterface');
        $this->_scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_menuFactory = $objectManager->get('Magento\Backend\Model\MenuFactory');
        $this->_urlModel = $objectManager->get('Magento\Backend\Model\UrlInterface');
        $this->_moduleList = $objectManager->get('Magento\Framework\Module\ModuleListInterface');
        if ($this->_serializedSubmenu) {
            $this->_submenu = $this->_menuFactory->create();
            $this->_submenu->unserialize($this->_serializedSubmenu);
        }
    }
}
