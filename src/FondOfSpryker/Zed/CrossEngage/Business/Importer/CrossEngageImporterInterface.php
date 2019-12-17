<?php


namespace FondOfSpryker\Zed\CrossEngage\Business\Importer;


interface CrossEngageImporterInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param  string  $file
     * @param  string  $importPath
     * @return void
     */
    public function run(string $file): void;
}
