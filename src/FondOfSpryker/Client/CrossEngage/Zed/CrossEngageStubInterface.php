<?php

namespace FondOfSpryker\Client\CrossEngage\Zed;

use Generated\Shared\Transfer\CrossEngageTransfer;

interface CrossEngageStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer;
}
