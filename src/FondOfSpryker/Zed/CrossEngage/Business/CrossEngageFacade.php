<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageBusinessFactory getFactory()
 */
class CrossEngageFacade extends AbstractFacade implements CrossEngageFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return void
     */
    public function subscribeToCrossEngage(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()->createSubscriptionHandler()->subscribe($crossEngageTransfer);
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscriptionToCrossEngage(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()->createSubscriptionHandler()->confirmSubscription($crossEngageTransfer);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribeToCrossEngage(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()->createSubscriptionHandler()->unsubscribe($crossEngageTransfer);
    }
}
