<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Mapper;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;

class StateMapper
{
    public function getNumericState(string $state): ?int
    {
        if (!\array_key_exists($state, CrossEngageConstants::XNG_NUMERIC_STATES)) {
            return null;
        }

        return CrossEngageConstants::XNG_NUMERIC_STATES[$state];
    }
}
