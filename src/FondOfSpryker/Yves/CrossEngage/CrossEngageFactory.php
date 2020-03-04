<?php

namespace FondOfSpryker\Yves\CrossEngage;

use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use FondOfSpryker\Yves\CrossEngage\Form\CrossEngageSubscriptionForm;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractFactory;
use Symfony\Component\Form\FormInterface;

class CrossEngageFactory extends AbstractFactory
{
    /**
     * @throws
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCrossEngageSubscriptionForm(): FormInterface
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY)->create($this->createCrossEngageSubscriptionForm());
    }

    /**
     * @return string
     */
    protected function createCrossEngageSubscriptionForm(): string
    {
        return CrossEngageSubscriptionForm::class;
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     *
     * @throws
     */
    public function getStore(): Store
    {
        return  $this->getProvidedDependency(CrossEngageDependencyProvider::STORE);
    }

    /**
     * @return string
     */
    public function getStorename(): string
    {
        $storeName = \explode('_', $this->getStore()->getStoreName());

        return \ucfirst(\strtolower($storeName[0]));
    }

    /**
     * @return StoreTransferMapper
     */
    public function createStoreTransferMapper(): StoreTransferMapper
    {
        return new StoreTransferMapper($this->getStorename());
    }

    /**
     * @return \FondOfSpryker\Yves\CrossEngage\Model\UriLanguageKey\UriLanguageKeyPluginInterface[]
     *
     * @throws
     */
    public function getUrlLanguageKeyPlugins(): array
    {
        return $this->getProvidedDependency(CrossEngageDependencyProvider::URL_LANGUAGE_KEY_PLUGINS);
    }
}
