<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Api\Model\XngDefaultHeaderModel;
use FondOfSpryker\Zed\CrossEngage\Business\Subscription\SubscriptionHandler;
use FondOfSpryker\Zed\CrossEngage\CrossEngageDependencyProvider;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
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
        return new SubscriptionHandler($this->getConfig(), $this->createCrossEngageApiClient());
    }

    /**
     * @return \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageApiClient
     */
    protected function createCrossEngageApiClient(): CrossEngageApiClient
    {
        return new CrossEngageApiClient($this->getGuzzleClient(), $this->getConfig());
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
}
