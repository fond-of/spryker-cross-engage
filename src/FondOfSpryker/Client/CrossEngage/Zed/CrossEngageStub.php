<?php

namespace FondOfSpryker\Client\CrossEngage\Zed;

use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Client\ZedRequest\Stub\ZedRequestStub;

class CrossEngageStub extends ZedRequestStub implements CrossEngageStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->zedStub->call('/cross-engage/gateway/subscribe', $crossEngageTransfer);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->zedStub->call('/cross-engage/gateway/confirm-subscription', $crossEngageTransfer);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->zedStub->call('/cross-engage/gateway/unsubscribe', $crossEngageTransfer);
    }
}
