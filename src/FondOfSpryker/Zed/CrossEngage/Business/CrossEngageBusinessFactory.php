<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Api\Model\XngDefaultHeaderModel;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\CrossEngageResponseMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\StateMapper;
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
            $this->createStateMapper()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageApiClient
     */
    protected function createCrossEngageApiClient(): CrossEngageApiClient
    {
        return new CrossEngageApiClient(
            $this->getGuzzleClient(),
            $this->getConfig(),
            $this->createCrossEngageResponseMapper()
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
     * @return StateMapper
     */
    protected function createStateMapper(): StateMapper
    {
        return new StateMapper();
    }

    protected function createCrossEngageResponseMapper()
    {
        return new CrossEngageResponseMapper(
            $this->createStateMapper(),
            $this->getStorename()
        );
    }
}
