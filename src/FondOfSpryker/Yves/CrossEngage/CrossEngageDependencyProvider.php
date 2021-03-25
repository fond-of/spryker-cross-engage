<?php

namespace FondOfSpryker\Yves\CrossEngage;

use FondOfSpryker\Yves\CrossEngage\Dependency\Service\CrossEngageToNewsletterServiceBridge;
use FondOfSpryker\Yves\CrossEngage\Model\UriLanguageKey\UriLanguageKeyPluginUsStorePlugin;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

class CrossEngageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const STORE = 'STORE';
    public const URL_LANGUAGE_KEY_PLUGINS = 'URL_LANGUAGE_KEY_PLUGINS';
    public const SERVICE_NEWSLETTER = 'SERVICE_NEWSLETTER';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);
        $container[static::STORE] = $this->addStore();
        $container[static::URL_LANGUAGE_KEY_PLUGINS] = $this->addUrlLanguageKeyPlugins();
        $container = $this->addNewsletterService($container);

        return $container;
    }

    /**
     * @return \FondOfSpryker\Yves\CrossEngage\Model\UriLanguageKey\UriLanguageKeyPluginInterface[]
     */
    protected function addUrlLanguageKeyPlugins(): array
    {
        return [
            new UriLanguageKeyPluginUsStorePlugin(),
        ];
    }

    /**
     * @return Store
     */
    protected function addStore(): Store
    {
        return Store::getInstance();
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addNewsletterService(Container $container): Container
    {
        $container[static::SERVICE_NEWSLETTER] = function (Container $container) {
            return new CrossEngageToNewsletterServiceBridge($container->getLocator()->newsletter()->service());
        };

        return $container;
    }
}
