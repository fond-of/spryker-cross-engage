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
        $language = array_search($store->getCurrentLocale(), $store->getLocales());
        if ($language !== false) {
            return $language;
        }

        return '';
    }
}
