<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\StoreTransferMapper;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use Spryker\Zed\Kernel\Container;

class CrossEngageBusinessFactoryTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\CrossEngageConfig
     */
    protected $crossEngageConfigMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient
     */
    protected $crossEngageUserApiClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\Business\Mapper\StoreTransferMapper
     */
    protected $storeTransferMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageBusinessFactory
     */
    protected $crossEngageBusinessFactory;

    protected function _before()
    {
        parent::_before();

        $this->crossEngageConfigMock = $this->getMockBuilder(CrossEngageConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->crossEngageUserApiClientMock = $this->getMockBuilder(CrossEngageUserApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeTransferMapperMock = $this->getMockBuilder(StoreTransferMapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->crossEngageBusinessFactory = new CrossEngageBusinessFactory();
        $this->crossEngageBusinessFactory->setConfig($this->crossEngageConfigMock);
        $this->crossEngageBusinessFactory->setContainer($this->containerMock);
    }

    public function testCreateSubscriptionHandler(): void
    {

    }
}
