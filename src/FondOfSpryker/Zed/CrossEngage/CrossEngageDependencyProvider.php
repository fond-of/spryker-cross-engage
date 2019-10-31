<?php

namespace FondOfSpryker\Zed\CrossEngage;

use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleBridge;
use GuzzleHttp\Client as GuzzleClient;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CrossEngageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_GUZZLE = 'CLIENT_GUZZLE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addGuzzleClient($container);

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
                new GuzzleClient(
                    [
                    'base_uri' => $this->getConfig()->getCrossEngageApiUri(),
                    ]
                )
            );
        };

        return $container;
    }
}
