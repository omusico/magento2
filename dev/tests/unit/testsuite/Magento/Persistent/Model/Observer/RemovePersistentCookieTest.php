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
 
namespace Magento\Persistent\Model\Observer;
 
class RemovePersistentCookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RemovePersistentCookie
     */
    protected $model;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionModelMock;

    
    protected function setUp()
    {
        $this->persistentMock = $this->getMock('Magento\Persistent\Helper\Session', [], [], '', false);
        $this->sessionModelMock = $this->getMock('Magento\Persistent\Model\Session', [], [], '', false);
        $this->persistentDataMock = $this->getMock('Magento\Persistent\Helper\Data', [], [], '', false);
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->quoteManagerMock = $this->getMock('Magento\Persistent\Model\QuoteManager', [], [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->model = new RemovePersistentCookie(
            $this->persistentMock,
            $this->persistentDataMock,
            $this->customerSessionMock,
            $this->quoteManagerMock);
    }

    public function testExecuteWithPersistentDataThatCanNotBeProcess()
    {
        $this->persistentDataMock->expects($this->once())
            ->method('canProcess')->with($this->observerMock)->will($this->returnValue(false));
        $this->persistentMock->expects($this->never())->method('getSession');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenSessionIsNotPersistent()
    {
        $this->persistentDataMock->expects($this->once())
            ->method('canProcess')->with($this->observerMock)->will($this->returnValue(true));
        $this->persistentMock->expects($this->once())->method('isPersistent')->will($this->returnValue(false));

        $this->persistentMock->expects($this->never())->method('getSession');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWithNotLoggedInCustomer()
    {
        $this->persistentDataMock->expects($this->once())
            ->method('canProcess')->with($this->observerMock)->will($this->returnValue(true));
        $this->persistentMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->persistentMock->expects($this->once())
            ->method('getSession')->will($this->returnValue($this->sessionModelMock));
        $this->sessionModelMock->expects($this->once())->method('removePersistentCookie')->will($this->returnSelf());
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));
        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerId')->with(null)->will($this->returnSelf());
        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerGroupId')->with(null)->will($this->returnSelf());
        $this->quoteManagerMock->expects($this->once())->method('setGuest');

        $this->model->execute($this->observerMock);
    }

    public function testExecute()
    {
        $this->persistentDataMock->expects($this->once())
            ->method('canProcess')->with($this->observerMock)->will($this->returnValue(true));
        $this->persistentMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->persistentMock->expects($this->once())
            ->method('getSession')->will($this->returnValue($this->sessionModelMock));
        $this->sessionModelMock->expects($this->once())->method('removePersistentCookie')->will($this->returnSelf());
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->never())->method('setCustomerId');
        $this->customerSessionMock->expects($this->never())->method('setCustomerGroupId');
        $this->quoteManagerMock->expects($this->once())->method('setGuest');

        $this->model->execute($this->observerMock);
    }
}
