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
     * @param  \Generated\Shared\Transfer\CrossEngageTransfer  $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     * @throws
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()->createSubscriptionHandler()->subscribe($crossEngageTransfer);
    }

    /**
     * @param  CrossEngageTransfer  $crossEngageTransfer
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()->createSubscriptionHandler()->confirmSubscription($crossEngageTransfer);
    }

    /**
     * @param  CrossEngageTransfer  $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer
    {
        return $this->getFactory()->createSubscriptionHandler()->unsubscribe($crossEngageTransfer);
    }

    /**
     * @return string
     */
    public function getRegisteredImporterNames(): string
    {
        return $this->getFactory()->getImportHandler()->getImporterNames();
    }


    /**
     * @param  array  $importerNames
     * @param  array  $files
     * @return void
     */
    public function handleImporter(array $importerNames, array $files): void
    {
        $this->getFactory()->getImportHandler()->handle($importerNames, $files);
    }
}
