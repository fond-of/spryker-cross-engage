<?php

namespace FondOfSpryker\Client\CrossEngage;

use Generated\Shared\Transfer\CrossEngageTransfer;

interface CrossEngageClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer;
}
