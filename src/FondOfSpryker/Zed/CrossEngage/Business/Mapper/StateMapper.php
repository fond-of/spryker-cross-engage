<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Mapper;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;

class StateMapper
{
    public function getState(string $state): ?int
    {
        if (!array_key_exists(CrossEngageConstants::XNG_STATES)) {
            return null;
        }

        return CrossEngageConstants::XNG_STATES[$state];
    }
}
