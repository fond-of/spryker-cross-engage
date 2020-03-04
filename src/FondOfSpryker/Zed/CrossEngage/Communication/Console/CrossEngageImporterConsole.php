<?php

namespace FondOfSpryker\Zed\CrossEngage\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \FondOfSpryker\Zed\CrossEngage\Business\CrossEngageFacade getFacade()
 */
class CrossEngageImporterConsole extends Console
{
    public const COMMAND_NAME = 'crossengage:import';
    public const DESCRIPTION = 'Imports newsletter from CSV to CrossEngage';
    public const DESCRIPTION_ERROR = 'Not all required files are available (%s), please run propel:install to get them!';
    public const RESOURCE_OPTION = 'resource';
    public const RESOURCE_OPTION_SHORTCUT = 'r';
    public const RESOURCE_FILES_OPTION = 'files';
    public const RESOURCE_FILES_OPTION_SHORTCUT = 'f';

    protected $requiredClasses = [
        'Orm/Zed/Store/Persistence/SpyStoreQuery.php',
        'Orm/Zed/Store/Persistence/Base/SpyStoreQuery.php'
    ];

    /**
     * @return void
     */
    protected function configure()
    {
        $helpText = sprintf(static::DESCRIPTION_ERROR, implode(',', $this->requiredClasses));
        $description = $helpText;

        $this->addOption(
            static::RESOURCE_FILES_OPTION,
            static::RESOURCE_FILES_OPTION_SHORTCUT,
            InputArgument::OPTIONAL,
            'Defines the filenames'
        );

        if ($this->areFilesAvailable()) {
            try {
                $importerNames = $this->getFacade()->getRegisteredImporterNames();
            } catch (\Exception $exception) {
                $importerNames = '';
            }
            $this->addOption(
                static::RESOURCE_OPTION,
                static::RESOURCE_OPTION_SHORTCUT,
                InputArgument::OPTIONAL,
                sprintf('Defines the resource aka the importer to use. Available importer: %s', $importerNames)
            );
            $helpText = '';
            $description = static::DESCRIPTION;
        }

        $this->setName(static::COMMAND_NAME)
            ->setDescription($description)
            ->addUsage(sprintf('-%s resource_name -%s filename', static::RESOURCE_OPTION_SHORTCUT,
                static::RESOURCE_FILES_OPTION_SHORTCUT));

        $this->setHelp($helpText);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = static::CODE_SUCCESS;
        $messenger = $this->getMessenger();

        $files = [];
        if ($input->getOption(static::RESOURCE_FILES_OPTION)) {
            $file = $input->getOption(static::RESOURCE_FILES_OPTION);
            $files = explode(',', $file);
        }

        $importerNames = [];
        if ($input->getOption(static::RESOURCE_OPTION)) {
            $resourceString = $input->getOption(static::RESOURCE_OPTION);
            $importerNames = explode(',', $resourceString);
        }

        try {
            $this->getFacade()->handleImporter($importerNames, $files);
        } catch (\Exception $exception) {
            $status = static::CODE_ERROR;
        }

        $messenger->info(sprintf(
            'You just executed %s!',
            static::COMMAND_NAME
        ));

        return $status;
    }

    /**
     * @return bool
     */
    protected function areFilesAvailable(): bool
    {
        $path = sprintf('%s/../../../../../../../../../src', rtrim(__DIR__, '/'));
        foreach ($this->requiredClasses as $requiredClass) {
            $test = sprintf('%s/%s', $path, $requiredClass);
            if (!file_exists(sprintf('%s/%s', $path, $requiredClass))) {
                return false;
            }
        }
        return true;
    }
}
