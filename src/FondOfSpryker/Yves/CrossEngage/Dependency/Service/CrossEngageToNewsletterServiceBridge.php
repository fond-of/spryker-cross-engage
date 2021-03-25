<?php

namespace FondOfSpryker\Yves\CrossEngage\Dependency\Service;

use FondOfSpryker\Service\Newsletter\NewsletterServiceInterface;

class CrossEngageToNewsletterServiceBridge implements CrossEngageToNewsletterServiceInterface
{
    /**
     * @var \FondOfSpryker\Service\Newsletter\NewsletterServiceInterface
     */
    protected $service;

    /**
     * CrossEngageToNewsletterServiceBridge constructor.
     *
     * @param  \FondOfSpryker\Service\Newsletter\NewsletterServiceInterface  $newsletterService
     */
    public function __construct(NewsletterServiceInterface $newsletterService)
    {
        $this->service = $newsletterService;
    }

    /**
     * @param  String  $string
     *
     * @return string
     */
    public function getHash(String $string): string
    {
        return $this->service->getHash($string);
    }
}
