<?php

namespace FondOfSpryker\Zed\CrossEngage\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

interface CrossEngageToStoreFacadeInterface
{
    /**
     * @return StoreTransfer
     */
    public function getCurrentStore(): StoreTransfer;
}
