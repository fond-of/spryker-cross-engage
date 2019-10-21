<?php

namespace FondOfSpryker\Zed\CrossEngage\Communication\Controller;

use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $crossEngageTransfer
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    public function subscribeAction(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer
    {
        $this->getFacade()->subscribeToCrossEngage($crossEngageTransfer);

        return $crossEngageTransfer;
    }
}
