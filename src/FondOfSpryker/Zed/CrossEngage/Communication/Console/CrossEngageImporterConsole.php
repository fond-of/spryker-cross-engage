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
    public const RESOURCE_OPTION = 'resource';
    public const RESOURCE_OPTION_SHORTCUT = 'r';
    public const RESOURCE_FILES_OPTION = 'files';
    public const RESOURCE_FILES_OPTION_SHORTCUT = 'f';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            static::RESOURCE_FILES_OPTION,
            static::RESOURCE_FILES_OPTION_SHORTCUT,
            InputArgument::OPTIONAL,
            'Defines the filenames'
        );

        $this->addOption(
            static::RESOURCE_OPTION,
            static::RESOURCE_OPTION_SHORTCUT,
            InputArgument::OPTIONAL,
            sprintf('Defines the resource aka the importer to use. Available importer: %s', $this->getFacade()->getRegisteredImporterNames())
        );

        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::DESCRIPTION)
            ->addUsage(sprintf('-%s resource_name -%s filename', static::RESOURCE_OPTION_SHORTCUT,
                static::RESOURCE_FILES_OPTION_SHORTCUT));
    }

    /**
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
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
}
