<?php

namespace FondOfSpryker\Yves\CrossEngage;

use FondOfSpryker\Yves\CrossEngage\Model\UriLanguageKey\UriLanguageKeyPluginUsStorePlugin;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

class CrossEngageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const STORE = 'STORE';
    public const URL_LANGUAGE_KEY_PLUGINS = 'URL_LANGUAGE_KEY_PLUGINS';

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
}
