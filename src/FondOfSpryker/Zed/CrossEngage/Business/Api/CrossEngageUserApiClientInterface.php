<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Api;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Shared\Newsletter\NewsletterConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageEventHandler;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceInterface;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Generated\Zed\Ide\Newsletter;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

interface CrossEngageUserApiClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $transfer
     * @param array                                          $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer|null
     */
    public function fetchUser(CrossEngageTransfer $crossEngageTransfer, array $options = []): ?CrossEngageTransfer;

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param array               $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer
     */
    public function createUser(CrossEngageTransfer $crossEngageTransfer, array $options = []): CrossEngageResponseTransfer;

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param array               $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer
     */
    public function updateUser(CrossEngageTransfer $crossEngageTransfer, array $options = []): CrossEngageResponseTransfer;

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param string              $state
     * @param array               $options
     *
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer, string $state, array $options = []): CrossEngageResponseTransfer;

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param string              $state
     * @param array               $options
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer, string $state, array $options = []): CrossEngageResponseTransfer;
}
