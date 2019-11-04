<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Handler;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageEventApiClient;
use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
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
     * @param CrossEngageEventApiClient $eventApiClient
     * @param StoreTransferMapper       $storeTransferMapper
     */
    public function __construct(
        CrossEngageEventApiClient $eventApiClient,
        StoreTransferMapper $storeTransferMapper
    ) {
        $this->eventApiClient = $eventApiClient;
        $this->storeTransferMapper = $storeTransferMapper;
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

        $crossEngageNewsletterEventTransfer = new CrossEngageNewsletterEventTransfer();
        $crossEngageNewsletterEventTransfer
            ->setEmailNewsletter($this->storeTransferMapper->getStorename())
            ->setLanguage($crossEngageTransfer->getLanguage());

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
