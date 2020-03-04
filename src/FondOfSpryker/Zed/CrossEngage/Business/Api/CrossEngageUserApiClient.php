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

class CrossEngageUserApiClient implements CrossEngageUserApiClientInterface
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
     * @var CrossEngageToNewsletterServiceInterface
     */
    protected $newsletterService;


    /**
     * @param \FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface $guzzleClient
     * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig                                        $config
     * @param CrossEngageEventHandler                                                                 $engageEventHandler
     * @param StoreTransferMapper                                                                     $storeTransferMapper
     * @param CrossEngageToNewsletterServiceInterface                                                 $newsletterService
     */
    public function __construct(
        CrossEngageToGuzzleInterface $guzzleClient,
        CrossEngageConfig $config,
        CrossEngageEventHandler $engageEventHandler,
        StoreTransferMapper $storeTransferMapper,
        CrossEngageToNewsletterServiceInterface $newsletterService
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
        $this->engageEventHandler = $engageEventHandler;
        $this->storeTransferMapper = $storeTransferMapper;
        $this->newsletterService = $newsletterService;
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
        // create user
        if ($this->putUser($crossEngageTransfer) === false) {
            return (new CrossEngageResponseTransfer())
                ->setStatus(sprintf('could not create user %s', $crossEngageTransfer->getEmail()))
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        // sent opt-in
        if ($this->engageEventHandler->optIn($crossEngageTransfer) === false) {
            return (new CrossEngageResponseTransfer())
                ->setStatus(sprintf('could not send opt-in event for %s', $crossEngageTransfer->getEmail()))
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, CrossEngageConstants::XNG_STATE_EMAIL_SENT);
        $this->putUser($crossEngageTransfer);

        $crossEngageResponseTransfer = new CrossEngageResponseTransfer();
        $crossEngageResponseTransfer->setStatus(sprintf('user created with %s', $crossEngageTransfer->getEmail()));
        $crossEngageResponseTransfer->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_SUBSCRIBE);

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
            unset($bodyArray['uriLanguageKey']); // TODO: Using CrossEngageApiTransfer instead

            $json = json_encode($bodyArray);
            $hash = $this->newsletterService->getHash($crossEngageTransfer->getEmail());

            $response = $this->guzzleClient->put(
                $this->config->getCrossEngageApiUriCreateUser($hash),
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
        $errorResponse = $this->checkUserState($crossEngageTransfer, CrossEngageConstants::XNG_STATE_EMAIL_SENT);

        if ($errorResponse instanceof CrossEngageResponseTransfer) {
            return $errorResponse;
        }

        $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, $state);

        if ($this->putUser($crossEngageTransfer) === false) {
            return (new CrossEngageResponseTransfer())
                ->setStatus(sprintf('could not update user %s to %s', $crossEngageTransfer->getEmail(), $state))
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_SUBSCRIBE);
        }

        return (new CrossEngageResponseTransfer())
            ->setStatus(sprintf('user %s subscribed confirm', $crossEngageTransfer->getEmail()))
            ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_SUBSCRIPTION_CONFIRMED);
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
        // sent opt-out
        if ($this->engageEventHandler->optOut($crossEngageTransfer) === false) {
            return (new CrossEngageResponseTransfer())
                ->setStatus(sprintf('could not send opt-out event for %s', $crossEngageTransfer->getEmail()))
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        $crossEngageTransfer = $this->updateEmailNewsletterState($crossEngageTransfer, $state);

        // update user
        if ($this->putUser($crossEngageTransfer) === false) {
            return (new CrossEngageResponseTransfer())
                ->setStatus(sprintf('could not update user %s', $crossEngageTransfer->getEmail()))
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        return (new CrossEngageResponseTransfer())
            ->setStatus(sprintf('user %s unsubscribed (%s)', $crossEngageTransfer->getEmail(), $crossEngageTransfer->getBusinessUnit()))
            ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_UNSUBSCRIBED);
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

    /**
     * @param CrossEngageTransfer|null $crossEngageTransfer
     * @param string                   $state
     *
     * @return CrossEngageResponseTransfer|null
     */
    protected function checkUserState(?CrossEngageTransfer $crossEngageTransfer, string $state): ?CrossEngageResponseTransfer
    {
        if ($crossEngageTransfer === null) {
            return (new CrossEngageResponseTransfer)
                ->setStatus('no user entry found')
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        if ($this->storeTransferMapper->getEmailState($crossEngageTransfer) === null) {
            return (new CrossEngageResponseTransfer)
                ->setStatus(sprintf('user (%s) found, but not for %s', $crossEngageTransfer->getEmail(), $crossEngageTransfer->getBusinessUnit()))
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        if ($this->storeTransferMapper->getEmailState($crossEngageTransfer) !== $state) {
            return (new CrossEngageResponseTransfer)
                ->setStatus(sprintf('user (%s) wrong state', $crossEngageTransfer->getEmail()))
                ->setRedirectTo(NewsletterConstants::ROUTE_REDIRECT_NEWSLETTER_FAILURE);
        }

        return null;
    }
}
