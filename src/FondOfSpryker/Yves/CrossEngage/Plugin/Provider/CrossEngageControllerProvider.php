<?php

namespace FondOfSpryker\Yves\CrossEngage\Plugin\Provider;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use Silex\Application;
use Spryker\Yves\Kernel\BundleConfigResolverAwareTrait;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

/**
 * @method \FondOfSpryker\Yves\CrossEngage\CrossEngageConfig getConfig()
 */
class CrossEngageControllerProvider extends AbstractYvesControllerProvider
{
    use BundleConfigResolverAwareTrait;

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app): void
    {
        $locale = $app->offsetGet('locale');

        $this
            ->addFormRoute()                // form only
            ->addFormSubmitRoute()          // submit logic
            ->addSubscribeRoute($locale)    // redirect after submit (contentful)
            ->addConfirmSubscription()      // confirm by token
            ->addUnsubscribe();             // unsubscribe by token
            //->addSubscribeConfirmationRoute()
            //->addSubscribeRoute($locale);
            //->addConfirmationRoute($locale);
    }

    /**
     * @return $this
     */
    protected function addFormRoute(): self
    {
        $this->createController('/{newsletter}/form', CrossEngageConstants::ROUTE_CROSS_ENGAGE_FOOTER, 'CrossEngage', 'Index', 'form')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET|POST');

        return $this;
    }

    /**
     * @return $this
     */
    protected function addFormSubmitRoute(): self
    {
        $this->createController('/{newsletter}/submit', CrossEngageConstants::ROUTE_CROSS_ENGAGE_SUBMIT, 'CrossEngage', 'Index', 'submit')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET|POST');

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    protected function addSubscribeRoute(string $locale): self
    {
        $subscribePathPart = $this->getConfig()->getSubscribePath($locale);

        $this->createController(sprintf('/{newsletter}/%s', $subscribePathPart), CrossEngageConstants::ROUTE_CROSS_ENGAGE_SUBSCRIBE, 'CrossEngage', 'Index', 'subscribe')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET');

        return $this;
    }

    /**
     * @return $this
     */
    protected function addConfirmSubscription(): self
    {
        $this->createController('/{newsletter}/confirm-subscription', CrossEngageConstants::ROUTE_CROSS_ENGAGE_CONFIRM_SUBSCRIPTION, 'CrossEngage', 'Index', 'confirmSubscription')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET');

        return $this;
    }

    /**
     * @return $this
     */
    protected function addUnsubscribe(): self
    {
        $this->createController('/{newsletter}/unsubscribe-test', CrossEngageConstants::ROUTE_CROSS_ENGAGE_UNSUBSCRIBE, 'CrossEngage', 'Index', 'unsubscribe')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET');

        return $this;
    }
}
