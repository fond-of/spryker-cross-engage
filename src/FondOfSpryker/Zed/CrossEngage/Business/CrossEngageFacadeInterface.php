<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use Generated\Shared\Transfer\CrossEngageTransfer;

interface CrossEngageFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return void
     */
    public function subscribeToCrossEngage(CrossEngageTransfer $crossEngageTransfer): void;
}
