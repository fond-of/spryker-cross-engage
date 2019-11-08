<?php

namespace FondOfSpryker\Zed\CrossEngage\Dependency\Service;

use FondOfSpryker\Service\Newsletter\NewsletterServiceInterface;

class CrossEngageToNewsletterServiceBridge implements CrossEngageToNewsletterServiceInterface
{
    /**
     * @var NewsletterServiceInterface
     */
    private $newsletterService;

    /**
     * @param NewsletterServiceInterface $newsletterService
     */
    public function __construct(NewsletterServiceInterface $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function getHash(string $string): string
    {
        return $this->newsletterService->getHash($string);
    }

    /**
     * @param  array $params
     * @return string
     */
    public function buildOptInUrl(array $params = []): string
    {
        return $this->newsletterService->getOptInUrl($params);
    }

    /**
     * @param  array $params
     * @return string
     */
    public function buildOptOutUrl(array $params = []): string
    {
        return $this->newsletterService->getOptOutUrl($params);
    }
}
