<?php

namespace FondOfSpryker\Yves\CrossEngage\Model\UriLanguageKey;

use Spryker\Shared\Kernel\Store;

interface UriLanguageKeyPluginInterface
{
    /**
     * @param Store $store
     *
     * @return string
     */
    public function getLanguageKey(Store $store): string;
}
