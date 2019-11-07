<?php

namespace FondOfSpryker\Zed\CrossEngage\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\Store\Business\StoreFacadeInterface;

class CrossEngageToStoreFacadeBridge implements CrossEngageToStoreFacadeInterface
{
    /**
     * @var StoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param StoreFacadeInterface $storeFacade
     */
    public function __construct(StoreFacadeInterface $storeFacade)
    {
        $this->storeFacade = $storeFacade;
    }

    /**
     * @return StoreTransfer
     */
    public function getCurrentStore(): StoreTransfer
    {
        return $this->storeFacade->getCurrentStore();
    }
}
