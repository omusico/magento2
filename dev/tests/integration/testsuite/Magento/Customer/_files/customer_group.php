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
/** @var \Magento\Customer\Api\GroupRepositoryInterface $groupRepository */
$groupRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Customer\Api\GroupRepositoryInterface'
);

/** @var \Magento\Customer\Api\Data\GroupDataBuilder $builder */
$builder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    '\Magento\Customer\Api\Data\GroupDataBuilder'
);
$customerGroupBuilder = $builder->setCode('custom_group')->setTaxClassId(3);

/** @var \Magento\Customer\Api\Data\GroupInterface $customerGroup */
$customerGroup = new \Magento\Customer\Model\Data\Group($customerGroupBuilder);
$groupRepository->save($customerGroup);
