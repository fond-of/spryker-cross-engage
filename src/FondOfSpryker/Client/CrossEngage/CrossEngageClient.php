<?php

namespace FondOfSpryker\Client\CrossEngage;

use Generated\Shared\Transfer\CrossEngageResponseTransfer;
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
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()
            ->createCrossEngageStub()
            ->subscribe($crossEngageTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()
            ->createCrossEngageStub()
            ->confirmSubscription($crossEngageTransfer);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()
            ->createCrossEngageStub()
            ->unsubscribe($crossEngageTransfer);
    }
}
