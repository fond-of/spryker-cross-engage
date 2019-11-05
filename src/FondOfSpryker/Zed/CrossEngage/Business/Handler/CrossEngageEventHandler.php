<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Handler;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageEventApiClient;
use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Zed\CrossEngage\Business\Url\NewsletterUrlBuilder;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use Generated\Shared\Transfer\CrossEngageBaseEventTransfer;
use Generated\Shared\Transfer\CrossEngageEventTransfer;
use Generated\Shared\Transfer\CrossEngageNewsletterEventTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Shared\Url\UrlBuilderInterface;

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
     * @var UrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @var CrossEngageConfig
     */
    protected $config;

    /**
     * @param CrossEngageEventApiClient $eventApiClient
     * @param StoreTransferMapper $storeTransferMapper
     * @param NewsletterUrlBuilder $urlBuilder
     * @param CrossEngageConfig $config
     */
    public function __construct(
        CrossEngageEventApiClient $eventApiClient,
        StoreTransferMapper $storeTransferMapper,
        NewsletterUrlBuilder $urlBuilder,
        CrossEngageConfig $config
    ) {
        $this->eventApiClient = $eventApiClient;
        $this->storeTransferMapper = $storeTransferMapper;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
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

        $emailNewsletter = strtolower($this->config->getStore()->getStoreName());
        $emailNewsletter.= '-' . strtolower($crossEngageTransfer->getBusinessUnit());

        $crossEngageNewsletterEventTransfer = new CrossEngageNewsletterEventTransfer();
        $crossEngageNewsletterEventTransfer
            ->setEmailNewsletter($emailNewsletter)
            ->setLanguage($crossEngageTransfer->getLanguage())
            ->setOptInUrl($this->urlBuilder->buildOptInUrl($crossEngageTransfer))
            ->setOptOutUrl($this->urlBuilder->buildOptOutUrl($crossEngageTransfer));

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
        $crossEngageEventTransfer->setProperties(
            [
                $this->createCrossEngageNewsletterEvent($crossEngageTransfer)->toArray(true, true)
            ]
        );

        $eventsCollection = new \ArrayObject();
        $eventsCollection->append($crossEngageEventTransfer);

        $crossEngageBaseEventTransfer = (new CrossEngageBaseEventTransfer())
            ->setId(\sha1($crossEngageTransfer->getEmail()))
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
        $crossEngageBaseEvent->setId(\sha1($crossEngageTransfer->getEmail()));

        return $crossEngageBaseEvent;
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageNewsletterEventTransfer
     */
    protected function createCrossEngageNewsletterEvent(CrossEngageTransfer $crossEngageTransfer): CrossEngageNewsletterEventTransfer
    {
        $crossEngageNewsletterEventTransfer = new CrossEngageNewsletterEventTransfer();
        $crossEngageNewsletterEventTransfer->setEmailNewsletter($this->storeTransferMapper->getStorename());
        $crossEngageNewsletterEventTransfer->setLanguage($crossEngageTransfer->getLanguage());

        return $crossEngageNewsletterEventTransfer;
    }
}
