<?php

namespace FondOfSpryker\Zed\CrossEngage\Business;

use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;

interface CrossEngageFacadeInterface
{
    /**
     * @param  \Generated\Shared\Transfer\CrossEngageTransfer  $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function subscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;

    /**
     * @param  CrossEngageTransfer  $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function confirmSubscription(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;

    /**
     * @param  CrossEngageTransfer  $crossEngageTransfer
     *
     * @return CrossEngageResponseTransfer
     */
    public function unsubscribe(CrossEngageTransfer $crossEngageTransfer): CrossEngageResponseTransfer;

    /**
     * @return string
     */
    public function getRegisteredImporterNames(): string;

    /**
     * @param  array  $importerNames
     * @param  array  $files
     * @return void
     */
    public function handleImporter(array $importerNames, array $files): void;
}
