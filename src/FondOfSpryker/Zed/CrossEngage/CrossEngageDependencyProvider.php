<?php

namespace FondOfSpryker\Zed\CrossEngage;

use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleBridge;
use FondOfSpryker\Zed\CrossEngage\Dependency\Facade\CrossEngageToStoreFacadeBridge;
use GuzzleHttp\Client as GuzzleClient;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CrossEngageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_GUZZLE = 'CLIENT_GUZZLE';
    public const STORE_FACADE = 'STORE_FACADE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addGuzzleClient($container);
        $container = $this->addStoreFacade($container);

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
}
