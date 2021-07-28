<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageSubscriptionHandler;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use FondOfSpryker\Zed\CrossEngage\CrossEngageDependencyProvider;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToStoreFacadeBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceBridge;
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
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper
     */
    protected $storeTransferMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
     */
    protected $guzzleClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToStoreFacadeInterface
     */
    protected $storeFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceInterface
     */
    protected $newsletterServiceMock;

    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageBusinessFactory
     */
    protected $crossEngageBusinessFactory;

    /**
     * @return void
     */
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

        $this->storeFacadeMock = $this->getMockBuilder(CrossEngageToStoreFacadeBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->newsletterServiceMock = $this->getMockBuilder(CrossEngageToNewsletterServiceBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->crossEngageBusinessFactory = new class ($this->crossEngageConfigMock, $this->containerMock) extends CrossEngageBusinessFactory {
            /**
             * @var \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig
             */
            protected $configMock;

            /**
             * @var \Spryker\Zed\Kernel\Container
             */
            protected $containerMock;

            /**
             *  constructor.
             *
             * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig $config
             * @param \Spryker\Zed\Kernel\Container $container
             */
            public function __construct(CrossEngageConfig $config, Container $container)
            {
                $this->configMock = $config;
                $this->containerMock = $container;
            }

            /**
             * @return \Spryker\Zed\Kernel\AbstractBundleConfig
             */
            public function getConfig()
            {
                return $this->configMock;
            }

            /**
             * @return \Spryker\Zed\Kernel\Container
             */
            protected function getContainer(): Container
            {
                return $this->containerMock;
            }
        };
    }

    public function testCreateSubscriptionHandler(): void
    {
        $self = $this;
        $guzzleClientMock = $this->getMockBuilder(CrossEngageToGuzzleInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnCallback(
                static function ($arg) {
                    if ($arg === CrossEngageDependencyProvider::CLIENT_GUZZLE) {
                        return true;
                    }

                    if ($arg === CrossEngageDependencyProvider::STORE_FACADE) {
                        return true;
                    }

                    if ($arg === CrossEngageDependencyProvider::NEWSLETTER_SERVICE) {
                        return true;
                    }
                }
            );

        $this->containerMock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(
                static function ($arg) use ($self, $guzzleClientMock) {
                    if ($arg === CrossEngageDependencyProvider::CLIENT_GUZZLE) {
                        return $guzzleClientMock;
                    }

                    if ($arg === CrossEngageDependencyProvider::STORE_FACADE) {
                        return $self->storeFacadeMock;
                    }

                    if ($arg === CrossEngageDependencyProvider::NEWSLETTER_SERVICE) {
                        return $self->newsletterServiceMock;
                    }
                }
            );

        $subscriptionHandler = $this->crossEngageBusinessFactory->createSubscriptionHandler();

        $this->assertInstanceOf(CrossEngageSubscriptionHandler::class, $subscriptionHandler);
    }
}
