<?php

namespace FondOfSpryker\Yves\CrossEngage\Model\UriLanguageKey;

use Spryker\Shared\Kernel\Store;

class UriLanguageKeyPluginUsStorePlugin implements UriLanguageKeyPluginInterface
{
    /**
     * @param Store $store
     *
     * @return string
     */
    public function getLanguageKey(Store $store): string
    {
        if (\strpos($store->getStoreName(), '_US') !== false) {
            return 'us';
        }

        return '';
    }
}
