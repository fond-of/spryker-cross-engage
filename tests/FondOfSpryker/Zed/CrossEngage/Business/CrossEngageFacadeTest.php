<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageSubscriptionHandler;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;

class CrossEngageFacadeTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageFacade
     */
    protected $crossEngageFacade;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\Business\CrossEngageBusinessFactory
     */
    protected $crossEngageBusinessFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageSubscriptionHandler
     */
    protected $subscriptionHandlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CrossEngageTransfer
     */
    protected $crossEngageTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CrossEngageResponseTransfer
     */
    protected $crossEngageResponseTransferMock;


    protected function _before()
    {
        parent::_before();

        $this->crossEngageBusinessFactoryMock = $this->getMockBuilder(CrossEngageBusinessFactory::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->crossEngageTransferMock = $this->getMockBuilder(CrossEngageTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->crossEngageResponseTransferMock = $this->getMockBuilder(CrossEngageResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subscriptionHandlerMock = $this->getMockBuilder(CrossEngageSubscriptionHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->crossEngageFacade = new CrossEngageFacade();
        $this->crossEngageFacade->setFactory($this->crossEngageBusinessFactoryMock);
    }

    /**
     * @return void
     */
    public function testSubscribe(): void
    {
        $this->crossEngageBusinessFactoryMock->expects($this->atLeastOnce())
            ->method('createSubscriptionHandler')
            ->willReturn($this->subscriptionHandlerMock);

        $this->subscriptionHandlerMock->expects($this->atLeastOnce())
            ->method('subscribe')
            ->with($this->crossEngageTransferMock)
            ->willReturn($this->crossEngageResponseTransferMock);

        $this->assertInstanceOf(
            CrossEngageResponseTransfer::class,
            $this->crossEngageFacade->subscribe($this->crossEngageTransferMock)
        );
    }

    /**
     * @return void
     */
    public function testConfirmSubscription(): void
    {
        $this->crossEngageBusinessFactoryMock->expects($this->atLeastOnce())
            ->method('createSubscriptionHandler')
            ->willReturn($this->subscriptionHandlerMock);

        $this->subscriptionHandlerMock->expects($this->atLeastOnce())
            ->method('confirmSubscription')
            ->with($this->crossEngageTransferMock)
            ->willReturn($this->crossEngageResponseTransferMock);

        $this->assertInstanceOf(
            CrossEngageResponseTransfer::class,
            $this->crossEngageFacade->confirmSubscription($this->crossEngageTransferMock)
        );
    }

    /**
     * @return void
     */
    public function testUnsubscribe(): void
    {
        $this->crossEngageBusinessFactoryMock->expects($this->atLeastOnce())
            ->method('createSubscriptionHandler')
            ->willReturn($this->subscriptionHandlerMock);

        $this->subscriptionHandlerMock->expects($this->atLeastOnce())
            ->method('unsubscribe')
            ->with($this->crossEngageTransferMock)
            ->willReturn($this->crossEngageResponseTransferMock);

        $this->assertInstanceOf(
            CrossEngageResponseTransfer::class,
            $this->crossEngageFacade->unsubscribe($this->crossEngageTransferMock)
        );
    }
}
