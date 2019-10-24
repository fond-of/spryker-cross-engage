<?php
namespace FondOfSpryker\Yves\CrossEngage;

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
     * @return Store
     */
    protected function getStore(): Store
    {
        return Store::getInstance();
    }

    /**
     * @return string
     */
    public function getStorename(): string
    {
        $storeName = \explode('_', $this->getStore()->getStoreName());

        return \ucfirst(\strtolower($storeName[0]));
    }
}
