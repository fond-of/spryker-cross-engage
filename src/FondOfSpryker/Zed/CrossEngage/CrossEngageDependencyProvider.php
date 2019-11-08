<?php

namespace FondOfSpryker\Zed\CrossEngage;

use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToNewsletterFacadeBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToStoreFacadeBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterHashGeneratorBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceBridge;
use GuzzleHttp\Client as GuzzleClient;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CrossEngageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_GUZZLE = 'CLIENT_GUZZLE';
    public const STORE_FACADE = 'STORE_FACADE';
    public const NEWSLETTER_SERVICE = 'NEWSLETTER_SERVICE';

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
     * @param Container $container
     *
     * @return Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container[static::STORE_FACADE] = function (Container $container) {
            return new CrossEngageToStoreFacadeBridge($container->getLocator()->store()->facade());
        };

        return $container;
    }

    protected function addNewsletterService(Container $container): Container
    {
        $container[static::NEWSLETTER_SERVICE] = function (Container $container) {
            return new CrossEngageToNewsletterServiceBridge($container->getLocator()->newsletter()->service());
        };

        return $container;
    }
}
