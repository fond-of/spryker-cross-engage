<?php

namespace FondOfSpryker\Zed\CrossEngage\Dependency\Service;

interface CrossEngageToNewsletterServiceInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function getHash(string $string): string;

    /**
     * @param  array $params
     * @return string
     */
    public function buildOptInUrl(array $params = []): string;

    /**
     * @param  array $params
     * @return string
     */
    public function buildOptOutUrl(array $params = []): string;
}
