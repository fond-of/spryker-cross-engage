<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Handler;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageEventApiClient;
use Generated\Shared\Transfer\CrossEngageBaseEventTransfer;
use Generated\Shared\Transfer\CrossEngageEventTransfer;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;

class CrossEngageEventHandler
{
    /**
     * @var string
     */
    protected $storeName;

    /**
     * @var string
     */
    protected $setter;

    /**
     * @var string
     */
    protected $getter;

    /**
     * @var CrossEngageEventApiClient
     */
    protected $eventApiClient;

    /**
     * @param string $storeName
     * @param CrossEngageEventApiClient $eventApiClient
     */
    public function __construct(
        string $storeName,
        CrossEngageEventApiClient $eventApiClient
    ) {
        $this->storeName = $storeName;

        $key = 'emailNewsletterStateFor' . $this->storeName;
        $this->setter = 'set' . \ucfirst($key);
        $this->getter = 'get' . \ucfirst($key);
        $this->eventApiClient = $eventApiClient;
    }

    public function optIn(CrossEngageResponseTransfer $crossEngageResponseTransfer)
    {
        $getter = $this->getter;

        if (!array_key_exists($crossEngageResponseTransfer->$getter(), CrossEngageConstants::XNG_NUMERIC_STATES)) {
            return;
        }

        $numericState = CrossEngageConstants::XNG_NUMERIC_STATES[$crossEngageResponseTransfer->$getter()];

        if ($numericState > 2) {
            return;
        }

        $eventsCollection = new \ArrayObject();
        $eventsCollection->append((new CrossEngageEventTransfer())
            ->setEvent('Opt In')
            ->setProperties([
                'emailNewsletter' => $this->storeName,
            ])
        );

        $eventTransfer = (new CrossEngageBaseEventTransfer())
            ->setId(\sha1($crossEngageResponseTransfer->getEmail()))
            ->setEmail($crossEngageResponseTransfer->getEmail())
            ->setEvents($eventsCollection);

        $this->eventApiClient->postEvent($eventTransfer);
    }
}
