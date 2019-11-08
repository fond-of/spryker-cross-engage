<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageEventApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageEventHandler;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageSubscriptionHandler;
use FondOfSpryker\Zed\CrossEngage\Business\Url\NewsletterUrlBuilder;
use FondOfSpryker\Zed\CrossEngage\CrossEngageDependencyProvider;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToNewsletterFacadeInterface;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToStoreFacadeInterface;
use FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceInterface;
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
            $this->createCrossEngageEventHandler(),
            $this->createStoreTransferMapper(),
            $this->getNewsletterService()
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
     * @return CrossEngageToStoreFacadeInterface
     *
     * @throws
     */
    public function getStoreFacade(): CrossEngageToStoreFacadeInterface
    {
        return $this->getProvidedDependency(CrossEngageDependencyProvider::STORE_FACADE);
    }

    /**
     * @return CrossEngageToNewsletterServiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getNewsletterService(): CrossEngageToNewsletterServiceInterface
    {
        return $this->getProvidedDependency(CrossEngageDependencyProvider::NEWSLETTER_SERVICE);
    }

    /**
     * @return string
     */
    protected function getStorename(): string
    {
        $storeName = \explode('_', $this->getStoreFacade()->getCurrentStore()->getName());

        return \ucfirst(\strtolower($storeName[0]));
    }

    /**
     * @return CrossEngageEventHandler
     */
    protected function createCrossEngageEventHandler(): CrossEngageEventHandler
    {
        return new CrossEngageEventHandler(
            $this->createCrossEngageEventApiClient(),
            $this->createStoreTransferMapper(),
            $this->getStoreFacade(),
            $this->getConfig(),
            $this->getNewsletterService()
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
