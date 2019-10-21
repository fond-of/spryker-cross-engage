<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

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
    public function subscribeToCrossEngage(CrossEngageTransfer $crossEngageTransfer): void
    {
        $this->getFactory()->createSubscriptionHandler()->processNewsletterSubscriptions($crossEngageTransfer);
    }
}
