<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageEventApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Api\Model\XngDefaultHeaderModel;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageEventHandler;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\CrossEngageResponseMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\StateMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\StoreStateMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\StoreTransferMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Subscription\SubscriptionHandler;
use FondOfSpryker\Zed\CrossEngage\CrossEngageDependencyProvider;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig getConfig()
 */
class CrossEngageBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \FondOfSpryker\Zed\CrossEngage\Business\Subscription\SubscriptionHandler
     */
    public function createSubscriptionHandler(): SubscriptionHandler
    {
        return new SubscriptionHandler(
            $this->getConfig(),
            $this->createCrossEngageApiClient(),
            $this->createStoreTransferMapper()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient
     */
    protected function createCrossEngageApiClient(): CrossEngageUserApiClient
    {
        return new CrossEngageUserApiClient(
            $this->getGuzzleClient(),
            $this->getConfig(),
            $this->createCrossEngageResponseMapper(),
            $this->createCrossEngageEventHandler(),
            $this->createStoreTransferMapper()
        );
    }

    /**
     * @throws
     *
     * @return \FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface
     */
    public function getGuzzleClient(): CrossEngageToGuzzleInterface
    {
        return $this->getProvidedDependency(CrossEngageDependencyProvider::CLIENT_GUZZLE);
    }

    /**
     * @return \FondOfSpryker\Zed\CrossEngage\Business\Api\Model\XngDefaultHeaderModel
     */
    public function createXngDefaultHeaderModel(): XngDefaultHeaderModel
    {
        return new XngDefaultHeaderModel($this->getConfig());
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    public function getStore(): Store
    {
        return Store::getInstance();
    }

    /**
     * @return string
     */
    protected function getStorename(): string
    {
        $storeName = \explode('_', $this->getStore()->getStoreName());

        return \ucfirst(\strtolower($storeName[0]));
    }

    /**
     * @return CrossEngageResponseMapper
     */
    protected function createCrossEngageResponseMapper()
    {
        return new CrossEngageResponseMapper(
            $this->createStoreTransferMapper(),
            $this->getStorename()
        );
    }

    /**
     * @return CrossEngageEventHandler
     */
    protected function createCrossEngageEventHandler(): CrossEngageEventHandler
    {
        return new CrossEngageEventHandler(
            $this->createCrossEngageEventApiClient(),
            $this->createStoreTransferMapper()
        );
    }

    /**
     * @return CrossEngageEventApiClient
     */
    protected function createCrossEngageEventApiClient(): CrossEngageEventApiClient
    {
        return new CrossEngageEventApiClient(
            $this->getGuzzleClient(),
            $this->getConfig()
        );
    }

    /**
     * @return StoreTransferMapper
     */
    protected function createStoreTransferMapper(): StoreTransferMapper
    {
        return new StoreTransferMapper($this->getStorename());
    }
}
