<?php

namespace FondOfSpryker\Zed\CrossEngage;

use FondOfSpryker\Zed\CrossEngage\Business\CrossEngageBusinessFactory;
use FondOfSpryker\Zed\CrossEngage\Business\Handler\ImportHandler;
use FondOfSpryker\Zed\CrossEngage\Business\Importer\ActiveCampaignDataImporter;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToStoreFacadeBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceBridge;
use GuzzleHttp\Client as GuzzleClient;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\ClassResolver\Factory\FactoryResolver;
use Spryker\Zed\Kernel\Container;

class CrossEngageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_GUZZLE = 'CLIENT_GUZZLE';
    public const STORE_FACADE = 'STORE_FACADE';
    public const NEWSLETTER_SERVICE = 'NEWSLETTER_SERVICE';
    public const CROSS_ENGAGE_IMPORTER = 'CROSS_ENGAGE_IMPORTER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addGuzzleClient($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addNewsletterService($container);
        $container = $this->registerImporter($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addGuzzleClient(Container $container): Container
    {
        $container[static::CLIENT_GUZZLE] = function (Container $container) {
            return new CrossEngageToGuzzleBridge(
                new GuzzleClient(['base_uri' => $this->getConfig()->getCrossEngageApiUri()])
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container[static::STORE_FACADE] = function (Container $container) {
            return new CrossEngageToStoreFacadeBridge($container->getLocator()->store()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addNewsletterService(Container $container): Container
    {
        $container[static::NEWSLETTER_SERVICE] = function (Container $container) {
            return new CrossEngageToNewsletterServiceBridge($container->getLocator()->newsletter()->service());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \FondOfSpryker\Zed\CrossEngage\Business\Importer\CrossEngageImporterInterface[]
     */
    protected function registerImporterExtend(Container $container): array
    {
        return [
            new ActiveCampaignDataImporter($this->getFactory()->createCrossEngageApiClient(), $container->getLocator()->newsletter()->service()),
        ];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    private function registerImporter(Container $container): Container
    {
        $container[static::CROSS_ENGAGE_IMPORTER] = function (Container $container) {
            $importerCollection = new ImportHandler($this->getConfig()->getImportPath());

            foreach ($this->registerImporterExtend($container) as $importer) {
                $importerCollection->registerImporter($importer);
            }

            return $importerCollection;
        };

        return $container;
    }

    /**
     * @var \Spryker\Zed\Kernel\Business\BusinessFactoryInterface
     */
    private $factory;

    /**
     * @return \Spryker\Zed\Kernel\Business\BusinessFactoryInterface
     */
    protected function getFactory()
    {
        if ($this->factory === null) {
            $this->factory = $this->resolveFactory();
        }

        return $this->factory;
    }

    /**
     * @return \Spryker\Zed\Kernel\Business\AbstractBusinessFactory
     */
    private function resolveFactory()
    {
        /** @var \Spryker\Zed\Kernel\Business\AbstractBusinessFactory $factory */
        $factory = $this->getFactoryResolver()->resolve(CrossEngageBusinessFactory::class);

        return $factory;
    }

    /**
     * @return \Spryker\Zed\Kernel\ClassResolver\Factory\FactoryResolver
     */
    private function getFactoryResolver()
    {
        return new FactoryResolver();
    }
}
