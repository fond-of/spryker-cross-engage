<?php

namespace FondOfSpryker\Client\CrossEngage;

use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \FondOfSpryker\Client\CrossEngage\CrossEngageFactory getFactory()
 */
class CrossEngageClient extends AbstractClient implements CrossEngageClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer
    {
        return $this->getFactory()->createCrossEngageStub()->subscribe($crossEngageTransfer);
    }
}
