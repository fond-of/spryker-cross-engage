<?php


namespace FondOfSpryker\Zed\CrossEngage\Business\Handler;


use FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface;

interface ImportHandlerInterface
{

    /**
     * @param  \FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface  $crossEngageImporter
     * @return array|\FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface[]
     */
    public function registerImporter(CrossEngageImporterInterface $crossEngageImporter);

    /**
     * @param  string[]  $importerNames
     * @param  string[]  $files
     * @return void
     */
    public function handle(array $importerNames, array $files): void;

    /**
     * @return string
     */
    public function getImporterNames(): string;

}
