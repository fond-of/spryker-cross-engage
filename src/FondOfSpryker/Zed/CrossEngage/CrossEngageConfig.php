<?php

namespace FondOfSpryker\Zed\CrossEngage;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class CrossEngageConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->get(CrossEngageConstants::CROSS_ENGAGE_API_KEY);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->get(CrossEngageConstants::CROSS_ENGAGE_URL);
    }

    /**
     * @param string $locale
     *
     * @return int
     */
    public function getFormId(string $locale): int
    {
        return $this->getLocalized(CrossEngageConstants::CROSS_ENGAGE_FORM_ID, $locale);
    }

    /**
     * @param string $locale
     *
     * @return int
     */
    public function getListId(string $locale): int
    {
        return $this->getLocalized(CrossEngageConstants::CROSS_ENGAGE_LIST_ID, $locale);
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getSubscribePathPart(string $locale): string
    {
        return $this->getLocalized(CrossEngageConstants::CROSS_ENGAGE_SUBSCRIBE_PATH, $locale);
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

    /**
     * @return string
     */
    public function getCrossEngageApiUri(): string
    {
        return $this->get(CrossEngageConstants::CROSS_ENGAGE_API_URI, 'https://api.crossengage.io');
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function getCrossEngageApiUriFetchUser(string $id): string
    {
        return $this->get(CrossEngageConstants::CROSS_ENGAGE_API_URI_FETCH_USER, sprintf('users/%s', $id));
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function getCrossEngageApiUriCreateUser(string $id): string
    {
        return $this->getCrossEngageApiUriFetchUser($id);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function getCrossEngageApiUriEvents(): string
    {
        return $this->get(CrossEngageConstants::CROSS_ENGAGE_API_URI_EVENTS, sprintf('events'));
    }

    /**
     * @return string
     */
    public function getCrossEngageApiKey(): string
    {
        return $this->get(CrossEngageConstants::CROSS_ENGAGE_API_KEY);
    }

    /**
     * @return array
     */
    public function getXngHeader(): array
    {
        $defaultHeaders = [
            'headers' => [
                CrossEngageConstants::XNG_HEADER_FIELD_CONTENT_TYPE => 'application/json',
                CrossEngageConstants::XNG_HEADER_FIELD_API_VERSION => 1,
                CrossEngageConstants::XNG_HEADER_FIELD_AUTH_TOKEN => $this->getCrossEngageApiKey(),
            ],
        ];

        return $this->get(CrossEngageConstants::CROSS_ENGAGE_API_HEADER, $defaultHeaders);
    }

    /**
     * @return array
     */
    public function getXngRequestOptions(): array
    {
        return [
            'request.options' => [
                'exceptions' => false,
            ],
        ];
    }
}
