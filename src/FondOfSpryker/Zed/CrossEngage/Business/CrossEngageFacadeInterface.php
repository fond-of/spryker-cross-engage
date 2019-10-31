<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;

interface CrossEngageFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function subscribeToCrossEngage(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscriptionToCrossEngage(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;
}
