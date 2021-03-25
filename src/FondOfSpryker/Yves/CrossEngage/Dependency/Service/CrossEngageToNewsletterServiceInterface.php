<?php

namespace FondOfSpryker\Yves\CrossEngage\Dependency\Service;

interface CrossEngageToNewsletterServiceInterface
{
    /**
     * @param  String  $string
     *
     * @return string
     */
    public function getHash(String $string): string;
}
