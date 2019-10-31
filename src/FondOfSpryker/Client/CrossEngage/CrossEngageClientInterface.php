<?php

namespace FondOfSpryker\Client\CrossEngage;

use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;

interface CrossEngageClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;
}
