<?php


namespace FondOfSpryker\Zed\CrossEngage\Business\Handler;


use FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface;
use FondOfSpryker\Zed\CrossEngage\Exception\ImporterNotFoundException;
use FondOfSpryker\Zed\CrossEngage\Exception\ImportFileNotExistsException;

class ImportHandler implements ImportHandlerInterface
{
    /**
     * @var array|CrossEngageImporterInterface[]
     */
    protected $importer = [];

    /**
     * @var string
     */
    protected $importPath;

    /**
     * ImportHandler constructor.
     * @param  string  $importPath
     */
    public function __construct(string $importPath)
    {
        $this->importPath = $importPath;
    }

    /**
     * @param  \FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface  $crossEngageImporter
     * @return array|\FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface[]
     */
    public function registerImporter(CrossEngageImporterInterface $crossEngageImporter)
    {
        $this->importer[$crossEngageImporter->getName()] = $crossEngageImporter;
        return $this->importer;
    }

    /**
     * @param  string[]  $importerNames
     * @param  string[]  $files
     * @return void
     */
    public function handle(array $importerNames, array $files): void
    {
        $this->validateImporter($importerNames);

        foreach ($importerNames as $importerName) {
            foreach ($files as $file){
                $this->getImporterByName($importerName)->run($this->getRealFile($file));
            }
        }
    }

    /**
     * @return array
     */
    public function getImporter(): array
    {
        return $this->importer;
    }

    /**
     * @param  string  $importerName
     * @return \FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface
     * @throws \FondOfSpryker\Zed\CrossEngage\Exception\ImporterNotFoundException
     */
    public function getImporterByName(string $importerName): CrossEngageImporterInterface
    {
        if (!array_key_exists($importerName, $this->getImporter())) {
            throw new ImporterNotFoundException(sprintf('Importer with name %s not found!', $importerName));
        }
        return $this->importer[$importerName];
    }

    /**
     * @return string
     */
    public function getImporterNames(): string
    {
        return implode(',', array_keys($this->getImporter()));
    }

    /**
     * @param  array  $importerNames
     * @return void
     * @throws \FondOfSpryker\Zed\CrossEngage\Exception\ImporterNotFoundException
     */
    protected function validateImporter(array $importerNames): void
    {
        foreach ($importerNames as $importerName) {
            $this->getImporterByName($importerName);
        }
    }

    /**
     * @param  string  $file
     * @return string
     * @throws \FondOfSpryker\Zed\CrossEngage\Exception\ImportFileNotExistsException
     */
    protected function getRealFile(string $file): string
    {
        if ($this->fileExists($file)) {
            return $file;
        }

        $fileWithPath = sprintf('%s/%s', rtrim($this->importPath, '/'), $file);
        if ($this->fileExists($fileWithPath)) {
            return $fileWithPath;
        }

        $fileWithPathAbsolute = sprintf('%s/%s/%s', $this->getAbsoluteDirectory(), rtrim($this->importPath, '/'), $file);
        if ($this->fileExists($fileWithPathAbsolute)) {
            return $fileWithPathAbsolute;
        }

        throw new ImportFileNotExistsException();

    }

    /**
     * @return string
     */
    protected function getAbsoluteDirectory(): string
    {
        return sprintf('%s/../../../../../../../../..', __DIR__);
    }

    /**
     * @param  string  $file
     * @return bool
     */
    protected function fileExists(string $file): bool
    {
        return file_exists($file);
    }

}
