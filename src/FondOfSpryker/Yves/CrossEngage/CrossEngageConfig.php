<?php

namespace FondOfSpryker\Yves\CrossEngage;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use Spryker\Yves\Kernel\AbstractBundleConfig;

class CrossEngageConfig extends AbstractBundleConfig
{
    /**
     * @param string $locale
     *
     * @return string
     */
    public function getSubscribePath(string $locale): string
    {
        return $this->getLocalized(CrossEngageConstants::CROSS_ENGAGE_SUBSCRIBE_PATH, $locale);
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getConfirmationPath(string $locale): string
    {
        return $this->getLocalized(CrossEngageConstants::CROSS_ENGAGE_CONFIRMATION_PATH, $locale);
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getAlreadySubscribed(string $locale): string
    {
        return $this->getLocalized(CrossEngageConstants::CROSS_ENGAGE_ALREADY_SUBSCRIBED_PATH, $locale);
    }

    /**
     * @param string $key
     * @param string $locale
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getLocalized(string $key, string $locale, $default = null)
    {
        $localizedConfigs = $this->get(CrossEngageConstants::CROSS_ENGAGE_LOCALIZED_CONFIGS, []);

        if (!\is_array($localizedConfigs) || empty($localizedConfigs)) {
            return $default;
        }

        if (!\array_key_exists($locale, $localizedConfigs) || !\is_array($localizedConfigs[$locale])) {
            return $default;
        }

        $configs = $localizedConfigs[$locale];

        if (!\array_key_exists($key, $configs)) {
            return $default;
        }

        return $configs[$key];
    }
}
