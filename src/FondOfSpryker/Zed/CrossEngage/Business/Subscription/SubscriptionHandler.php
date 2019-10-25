<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Subscription;

use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\StateMapper;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;

/**
 * @method \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageBusinessFactory getFactory()
 */
class SubscriptionHandler
{
    /**
     * @var \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig
     */
    protected $config;

    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient
     */
    protected $crossEngageApiClient;

    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Business\Mapper\StateMapper
     */
    protected $mapper;

    /**
     * SubscriptionHandler constructor.
     *
     * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig $config
     * @param \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient $crossEngageApiClient
     * @param \FondOfSpryker\Zed\CrossEngage\Business\Mapper\StateMapper $mapper
     */
    public function __construct(CrossEngageConfig $config, CrossEngageUserApiClient $crossEngageApiClient, StateMapper $mapper)
    {
        $this->config = $config;
        $this->crossEngageApiClient = $crossEngageApiClient;
        $this->mapper = $mapper;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function processNewsletterSubscriptions(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        $crossEngageResponseTransfer = $this->crossEngageApiClient->fetchUser($crossEngageTransfer);

        if ($crossEngageResponseTransfer === null) {
            return $this->crossEngageApiClient->createUser($crossEngageTransfer);
        }

        if ($crossEngageResponseTransfer instanceof CrossEngageResponseTransfer) {
            return $this->crossEngageApiClient->updateUser($crossEngageTransfer);
        }

        return $crossEngageResponseTransfer;
    }
}
