<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Handler;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Shared\Newsletter\NewsletterConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClient;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClientInterface;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Shared\Url\UrlBuilder;

/**
 * @method \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageBusinessFactory getFactory()
 */
class CrossEngageSubscriptionHandler
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
     * CrossEngageSubscriptionHandler constructor.
     * @param  \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig  $config
     * @param  \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClientInterface  $crossEngageApiClient
     * @param  \FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper  $storeTransferMapper
     */
    public function __construct(
        CrossEngageConfig $config,
        CrossEngageUserApiClientInterface $crossEngageApiClient,
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
     * @throws \Exception
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        $fetchedCrossEngageTransfer = $this->crossEngageApiClient->fetchUser($crossEngageTransfer);

        if ($fetchedCrossEngageTransfer === null) {
            return $this->crossEngageApiClient->createUser($crossEngageTransfer);
        }

        if ($this->storeTransferMapper->getNumericState($crossEngageTransfer) <= 2) {
            $fetchedCrossEngageTransfer->fromArray($crossEngageTransfer->toArray(), true);
            $fetchedCrossEngageTransfer = $this->storeTransferMapper->updateEmailState($crossEngageTransfer, CrossEngageConstants::XNG_STATE_NEW);

            return $this->crossEngageApiClient->updateUser($fetchedCrossEngageTransfer);
        }

        return (new CrossEngageResponseTransfer())
            ->setStatus(sprintf('user (%s) already exists', $crossEngageTransfer->getEmail()))
            ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_ALREADY_SUBSCRIBED);
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
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        if ($crossEngageTransfer instanceof CrossEngageTransfer) {
            return $this->crossEngageApiClient->confirmSubscription(
                $crossEngageTransfer,
                CrossEngageConstants::XNG_STATE_SUBSCRIBED
            );
        }

        return (new CrossEngageResponseTransfer())
            ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
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
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        if ($crossEngageTransfer instanceof CrossEngageTransfer) {
            return $this->crossEngageApiClient->unsubscribe(
                $crossEngageTransfer,
                CrossEngageConstants::XNG_STATE_UNSUBSCRIBED
            );
        }

        return (new CrossEngageResponseTransfer())
            ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
    }
}
