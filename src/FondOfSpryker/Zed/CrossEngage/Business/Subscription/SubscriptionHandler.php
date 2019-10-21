<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Subscription;

use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageApiClient;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
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
     * @var \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageApiClient
     */
    protected $crossEngageApiClient;

    /**
     * SubscriptionHandler constructor.
     *
     * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig $config
     * @param \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageApiClient $crossEngageApiClient
     */
    public function __construct(CrossEngageConfig $config, CrossEngageApiClient $crossEngageApiClient)
    {
        $this->config = $config;
        $this->crossEngageApiClient = $crossEngageApiClient;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return void
     */
    public function processNewsletterSubscriptions(CrossEngageTransfer $crossEngageTransfer): void
    {
        $crossEngageResponseTransfer = $this->crossEngageApiClient->fetchUser($crossEngageTransfer);

        if ($crossEngageResponseTransfer === null) {
            $this->crossEngageApiClient->createUser($crossEngageTransfer);
        }

        if ($crossEngageResponseTransfer instanceof CrossEngageTransfer) {
            $this->crossEngageApiClient->updateUser($crossEngageTransfer);
        }
    }
}
