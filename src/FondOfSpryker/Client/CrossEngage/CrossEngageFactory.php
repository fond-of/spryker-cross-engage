<?php

namespace FondOfSpryker\Client\CrossEngage;

use FondOfSpryker\Client\CrossEngage\Zed\CrossEngageStub;
use FondOfSpryker\Client\CrossEngage\Zed\CrossEngageStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class CrossEngageFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Client\CrossEngage\Zed\CrossEngageStubInterface
     */
    public function createCrossEngageStub(): CrossEngageStubInterface
    {
        return new CrossEngageStub($this->getZedRequestClient());
    }

    /**
     * @throws
     *
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    public function getZedRequestClient(): ZedRequestClientInterface
    {
        return $this->getProvidedDependency(CrossEngageDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
