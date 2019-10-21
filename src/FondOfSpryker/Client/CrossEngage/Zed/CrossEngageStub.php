<?php

namespace FondOfSpryker\Client\CrossEngage\Zed;

use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Client\ZedRequest\Stub\ZedRequestStub;

class CrossEngageStub extends ZedRequestStub implements CrossEngageStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer
    {
        return $this->zedStub->call('/cross-engage/gateway/subscribe', $crossEngageTransfer);
    }
}
