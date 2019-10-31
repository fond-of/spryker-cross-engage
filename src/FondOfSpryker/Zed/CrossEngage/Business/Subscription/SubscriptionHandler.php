<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Subscription;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\StoreTransferMapper;
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
     * @var StoreTransferMapper
     */
    private $storeTransferMapper;

    /**
     * SubscriptionHandler constructor.
     *
     * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig                     $config
     * @param \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient $crossEngageApiClient
     * @param \FondOfSpryker\Zed\CrossEngage\Business\Mapper\StateMapper           $mapper
     */
    public function __construct(
        CrossEngageConfig $config,
        CrossEngageUserApiClient $crossEngageApiClient,
        StoreTransferMapper $storeTransferMapper
    ) {
        $this->config = $config;
        $this->crossEngageApiClient = $crossEngageApiClient;
        $this->storeTransferMapper = $storeTransferMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        $fetchedCrossEngageTransfer = $this->crossEngageApiClient->fetchUser($crossEngageTransfer);

        if ($fetchedCrossEngageTransfer === null) {
            return $this->crossEngageApiClient->createUser($crossEngageTransfer);
        }

        return new CrossEngageResponseTransfer();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        $externalId = $crossEngageTransfer->getExternalId();
        $crossEngageTransfer = $this->crossEngageApiClient->fetchUser($crossEngageTransfer);

        if ($crossEngageTransfer === null) {
            return (new CrossEngageResponseTransfer())
                ->setStatus(sprintf('no user found for external-id %s', $externalId))
                ->setRedirectTo(CrossEngageConstants::ROUTE_CROSS_ENGAGE_CONFIRM_SUBSCRIPTION);
        }

        if ($crossEngageTransfer instanceof CrossEngageTransfer) {
            $this->crossEngageApiClient->confirmSubscription($crossEngageTransfer, CrossEngageConstants::XNG_STATE_SUBSCRIBED);
        }

        return new CrossEngageResponseTransfer();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        $externalId = $crossEngageTransfer->getExternalId();
        $crossEngageTransfer = $this->crossEngageApiClient->fetchUser($crossEngageTransfer);

        if ($crossEngageTransfer === null) {
            return (new CrossEngageResponseTransfer())
                ->setStatus(sprintf('no user found for external-id %s', $externalId))
                ->setRedirectTo(CrossEngageConstants::ROUTE_CROSS_ENGAGE_CONFIRM_SUBSCRIPTION);
        }

        if ($crossEngageTransfer instanceof CrossEngageTransfer) {
            $this->crossEngageApiClient->unsubscribe($crossEngageTransfer, CrossEngageConstants::XNG_STATE_UNSUBSCRIBED);
        }

        return new CrossEngageResponseTransfer();
    }
}
