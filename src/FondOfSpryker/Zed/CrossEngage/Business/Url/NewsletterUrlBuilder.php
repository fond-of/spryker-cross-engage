<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Url;

use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use Generated\Shared\Transfer\CrossEngageTransfer;

class NewsletterUrlBuilder
{
    public const BUNDLE = 'newsletter';

    /**
     * @var CrossEngageConfig
     */
    protected $config;

    /**
     * NewsletterUrlBuilder constructor.
     *
     * @param CrossEngageConfig $config
     */
    public function __construct(CrossEngageConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return string
     */
    public function buildOptInUrl(CrossEngageTransfer $crossEngageTransfer): string
    {
        return $this->build($crossEngageTransfer, $this->config->getOptInPathSegement());
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return string
     */
    public function buildOptOutUrl(CrossEngageTransfer $crossEngageTransfer): string
    {
        return $this->build($crossEngageTransfer, $this->config->getOptOutPathSegement());
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @param  string              $pathSegment
     * @return string
     */
    protected function build(CrossEngageTransfer $crossEngageTransfer, string $pathSegment): string
    {
        $url = $crossEngageTransfer->getHost();
        $url.= '/' . $crossEngageTransfer->getLanguage();
        $url.= '/' . static::BUNDLE;
        $url.= '/' . $pathSegment;
        $url.= '/' . \sha1($crossEngageTransfer->getEmail());

        return $url;
    }
}
