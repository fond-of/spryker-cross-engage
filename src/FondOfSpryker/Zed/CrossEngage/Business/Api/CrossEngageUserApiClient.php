<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Api;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Shared\Newsletter\NewsletterConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\CrossEngageEventHandler;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\CrossEngageResponseMapper;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

class CrossEngageUserApiClient
{
    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface
     */
    protected $guzzleClient;

    /**
     * @var \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig
     */
    protected $config;

    /**
     * @var CrossEngageEventHandler
     */
    protected $engageEventHandler;

    /**
     * @var StoreTransferMapper
     */
    protected $storeTransferMapper;

    /**
     * @param \FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface $guzzleClient
     * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig                                        $config
     * @param CrossEngageEventHandler                                                                 $engageEventHandler
     * @param StoreTransferMapper                                                                     $storeTransferMapper
     */
    public function __construct(
        CrossEngageToGuzzleInterface $guzzleClient,
        CrossEngageConfig $config,
        CrossEngageEventHandler $engageEventHandler,
        StoreTransferMapper $storeTransferMapper
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
        $this->engageEventHandler = $engageEventHandler;
        $this->storeTransferMapper = $storeTransferMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $transfer
     * @param array                                          $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer|null
     */
    public function fetchUser(CrossEngageTransfer $crossEngageTransfer, array $options = []): ?CrossEngageTransfer
    {
        try {
            $response = $this->guzzleClient->get(
                $this->config->getCrossEngageApiUriFetchUser($crossEngageTransfer->getExternalId()),
                array_merge(
                    $this->config->getXngHeader(),
                    $this->config->getXngRequestOptions(),
                    $options
                )
            );

            $content = json_decode($response->getBody()->getContents(), true);

            return $crossEngageTransfer->fromArray($content, true);
        } catch (RequestException $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param array               $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer
     */
    public function createUser(CrossEngageTransfer $crossEngageTransfer, array $options = []): CrossEngageResponseTransfer
    {
        if ($this->putUser($crossEngageTransfer) === true) {
            if ($this->engageEventHandler->optIn($crossEngageTransfer)) {
                $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, CrossEngageConstants::XNG_STATE_EMAIL_SENT);
                $this->putUser($crossEngageTransfer);
            }
        }

        $crossEngageResponseTransfer = new CrossEngageResponseTransfer();
        $crossEngageResponseTransfer->setStatus(sprintf('user created with ID %s', \sha1($crossEngageTransfer->getEmail())));
        $crossEngageResponseTransfer->setRedirectTo(NewsletterConstants::ROUTE_NEWSLETTER_SUBSCRIBE_SUCCESS);

        return $crossEngageResponseTransfer;
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param array               $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer
     */
    public function updateUser(CrossEngageTransfer $crossEngageTransfer, array $options = []): CrossEngageResponseTransfer
    {
        return $this->createUser($crossEngageTransfer, $options);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param array               $options
     *
     * @return bool
     */
    protected function putUser(CrossEngageTransfer $crossEngageTransfer, $options = []): bool
    {
        try {
            $bodyArray = $crossEngageTransfer->toArray(false, true);
            unset($bodyArray['host']); // TODO: Using CrossEngageApiTransfer instead
            $json = json_encode($bodyArray);

            $response = $this->guzzleClient->put(
                $this->config->getCrossEngageApiUriCreateUser(\sha1($crossEngageTransfer->getEmail())),
                array_merge(
                    $this->config->getXngHeader(),
                    $this->config->getXngRequestOptions(),
                    $options,
                    ['body' => $json]
                )
            );

            return $response->getStatusCode() === Response::HTTP_OK;
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param string              $state
     * @param array               $options
     *
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer, string $state, array $options = []): CrossEngageResponseTransfer
    {
        if ($this->storeTransferMapper->getEmailState($crossEngageTransfer) !== CrossEngageConstants::XNG_STATE_EMAIL_SENT) {
            return (new CrossEngageResponseTransfer)
                ->setStatus('user wrong state')
                ->setRedirectTo(CrossEngageConstants::ROUTE_CROSS_ENGAGE_SUBSCRIBE_FAILED);
        }

        $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, $state);

        if ($this->putUser($crossEngageTransfer) === true) {
            if ($this->engageEventHandler->optIn($crossEngageTransfer)) {
                $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, CrossEngageConstants::XNG_STATE_EMAIL_SENT);
                $this->putUser($crossEngageTransfer);
            }
        }

        $this->putUser($crossEngageTransfer);

        return new CrossEngageResponseTransfer();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param string              $state
     * @param array               $options
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer, string $state, array $options = []): CrossEngageResponseTransfer
    {
        if ($this->storeTransferMapper->getEmailState($crossEngageTransfer) !== CrossEngageConstants::XNG_STATE_SUBSCRIBED) {
            return (new CrossEngageResponseTransfer)
                ->setStatus('user wrong state')
                ->setRedirectTo(CrossEngageConstants::ROUTE_CROSS_ENGAGE_SUBSCRIBE_FAILED);
        }

        $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, $state);

        if ($this->putUser($crossEngageTransfer) === true) {
            if ($this->engageEventHandler->optOut($crossEngageTransfer)) {
                $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, CrossEngageConstants::XNG_STATE_UNSUBSCRIBED);
                $this->putUser($crossEngageTransfer);
            }
        }

        return new CrossEngageResponseTransfer();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param string              $state
     *
     * @return CrossEngageTransfer
     *
     * @throws
     */
    protected function updateEmailNewsletterState(CrossEngageTransfer $crossEngageTransfer, string $state): CrossEngageTransfer
    {
        return $this->storeTransferMapper->updateEmailState($crossEngageTransfer, $state);
    }
}
