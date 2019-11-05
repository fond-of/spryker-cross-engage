<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageEventApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageEventHandler;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageSubscriptionHandler;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\CrossEngageResponseMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Url\NewsletterUrlBuilder;
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
     * @return CrossEngageSubscriptionHandler
     */
    public function createSubscriptionHandler(): CrossEngageSubscriptionHandler
    {
        return new CrossEngageSubscriptionHandler(
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
            $this->createStoreTransferMapper(),
            $this->createUrlBuilder()
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

    /**
     * @return NewsletterUrlBuilder
     */
    protected function createUrlBuilder(): NewsletterUrlBuilder
    {
        return new NewsletterUrlBuilder($this->getConfig());
    }
}
