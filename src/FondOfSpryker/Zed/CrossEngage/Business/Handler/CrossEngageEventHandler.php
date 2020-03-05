<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Handler;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Shared\Newsletter\NewsletterConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageEventApiClient;
use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Url\NewsletterUrlBuilder;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToStoreFacadeInterface;
use FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceInterface;
use Generated\Shared\Transfer\CrossEngageBaseEventTransfer;
use Generated\Shared\Transfer\CrossEngageEventTransfer;
use Generated\Shared\Transfer\CrossEngageNewsletterEventTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;

class CrossEngageEventHandler
{
    /**
     * @var CrossEngageEventApiClient
     */
    protected $eventApiClient;

    /**
     * @var StoreTransferMapper
     */
    protected $storeTransferMapper;

    /**
     * @var CrossEngageConfig
     */
    protected $config;

    /**
     * @var CrossEngageToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @var CrossEngageToNewsletterServiceInterface
     */
    protected $newsletterService;

    /**
     * @param CrossEngageEventApiClient         $eventApiClient
     * @param StoreTransferMapper               $storeTransferMapper
     * @param CrossEngageToStoreFacadeInterface $storeFacade
     * @param CrossEngageConfig                 $config
     */
    public function __construct(
        CrossEngageEventApiClient $eventApiClient,
        StoreTransferMapper $storeTransferMapper,
        CrossEngageToStoreFacadeInterface $storeFacade,
        CrossEngageConfig $config,
        CrossEngageToNewsletterServiceInterface $newsletterService
    ) {
        $this->eventApiClient = $eventApiClient;
        $this->storeTransferMapper = $storeTransferMapper;
        $this->config = $config;
        $this->storeFacade = $storeFacade;
        $this->newsletterService = $newsletterService;
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return bool
     */
    public function optIn(CrossEngageTransfer $crossEngageTransfer): bool
    {
        if ($this->storeTransferMapper->getEmailState($crossEngageTransfer) !== CrossEngageConstants::XNG_STATE_NEW) {
            return false;
        }

        $emailNewsletter = strtolower($this->storeFacade->getCurrentStore()->getName());
        $emailNewsletter.= '-' . strtolower($crossEngageTransfer->getBusinessUnit());
        $hash = $this->newsletterService->getHash($crossEngageTransfer->getEmail());
        $uriLanguageKey = ($crossEngageTransfer->getUriLanguageKey()) ?: $crossEngageTransfer->getLanguage();

        $optInLink = $this->newsletterService->buildOptInUrl(
            [
                $uriLanguageKey,
                NewsletterConstants::NEWSTLETTER,
                $hash
            ]
        );

        $optOutLink = $this->newsletterService->buildOptOutUrl(
            [
                $uriLanguageKey,
                NewsletterConstants::NEWSTLETTER,
                $hash
            ]
        );

        $crossEngageNewsletterEventTransfer = new CrossEngageNewsletterEventTransfer();
        $crossEngageNewsletterEventTransfer
            ->setEmailNewsletter($emailNewsletter)
            ->setLanguage($crossEngageTransfer->getLanguage())
            ->setOptInUrl($optInLink)
            ->setOptOutUrl($optOutLink);

        $crossEngageEventTransfer = new CrossEngageEventTransfer();
        $crossEngageEventTransfer
            ->setEvent('Opt In')
            ->setProperties($crossEngageNewsletterEventTransfer);

        $crossEngageBaseEventTransfer = $this->createCrossEngageBaseEventTransfer($crossEngageTransfer);
        $crossEngageBaseEventTransfer->addEvents($crossEngageEventTransfer);

        return $this->eventApiClient->postEvent($crossEngageBaseEventTransfer);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return bool
     */
    public function optOut(CrossEngageTransfer $crossEngageTransfer): bool
    {
        $crossEngageEventTransfer = new CrossEngageEventTransfer();
        $crossEngageEventTransfer->setEvent('Opt Out');
        $crossEngageEventTransfer->setProperties($this->createCrossEngageNewsletterEvent($crossEngageTransfer));

        $eventsCollection = new \ArrayObject();
        $eventsCollection->append($crossEngageEventTransfer);

        $crossEngageBaseEventTransfer = (new CrossEngageBaseEventTransfer())
            ->setId($this->newsletterService->getHash($crossEngageTransfer->getEmail()))
            ->setEvents($eventsCollection);

        return $this->eventApiClient->postEvent($crossEngageBaseEventTransfer);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageBaseEventTransfer
     */
    protected function createCrossEngageBaseEventTransfer(CrossEngageTransfer $crossEngageTransfer): CrossEngageBaseEventTransfer
    {
        $crossEngageBaseEvent = new CrossEngageBaseEventTransfer();
        $crossEngageBaseEvent->setId($this->newsletterService->getHash($crossEngageTransfer->getEmail()));

        return $crossEngageBaseEvent;
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageNewsletterEventTransfer
     */
    protected function createCrossEngageNewsletterEvent(CrossEngageTransfer $crossEngageTransfer): CrossEngageNewsletterEventTransfer
    {
        $emailNewsletter = strtolower($this->storeFacade->getCurrentStore()->getName());
        $emailNewsletter.= '-' . strtolower($crossEngageTransfer->getBusinessUnit());

        $crossEngageNewsletterEventTransfer = new CrossEngageNewsletterEventTransfer();
        $crossEngageNewsletterEventTransfer->setEmailNewsletter($emailNewsletter);
        $crossEngageNewsletterEventTransfer->setLanguage($crossEngageTransfer->getLanguage());

        return $crossEngageNewsletterEventTransfer;
    }
}
