<?php

namespace FondOfSpryker\Yves\CrossEngage\Plugin\Provider;

use Silex\Application;
use Spryker\Yves\Kernel\BundleConfigResolverAwareTrait;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

/**
 * @method \FondOfSpryker\Yves\CrossEngage\CrossEngageConfig getConfig()
 */
class CrossEngageControllerProvider extends AbstractYvesControllerProvider
{
    use BundleConfigResolverAwareTrait;

    public const ROUTE_CROSS_ENGAGE_FOOTER = 'ROUTE_CROSS_ENGAGE_FOOTER';
    public const ROUTE_CROSS_ENGAGE_SUBMIT = 'ROUTE_CROSS_ENGAGE_SUBMIT';
    public const ROUTE_CROSS_ENGAGE_SUBSCRIBE = 'ROUTE_CROSS_ENGAGE_SUBSCRIBE';
    public const ROUTE_CROSS_ENGAGE_SUBSCRIBE_CONFIRM = 'ROUTE_CROSS_ENGAGE_SUBSCRIBE_CONFIRM';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app): void
    {
        $locale = $app->offsetGet('locale');

        $this->addFormRoute()
            ->addFormSubmitRoute()
            ->addSubscribeRoute($locale)
            ->addConfirmationRoute($locale);
    }

    /**
     * @return $this
     */
    protected function addFormSubmitRoute(): self
    {
        $this->createController('/{newsletter}/submit', static::ROUTE_CROSS_ENGAGE_SUBMIT, 'CrossEngage', 'Index', 'submit')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET|POST');

        return $this;
    }

    /**
     * @return $this
     */
    protected function addFormRoute(): self
    {
        $this->createController('/{newsletter}/form', static::ROUTE_CROSS_ENGAGE_FOOTER, 'CrossEngage', 'Index', 'form')
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

        $this->createController(sprintf('/{newsletter}/%s', $subscribePathPart), static::ROUTE_CROSS_ENGAGE_SUBSCRIBE, 'CrossEngage', 'Index', 'subscribe')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET');

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    protected function addConfirmationRoute(string $locale): self
    {
        $confirmationPathPart = $this->getConfig()->getConfirmationPath($locale);

        $this->createController(sprintf('/{newsletter}/%s', $confirmationPathPart), static::ROUTE_CROSS_ENGAGE_SUBSCRIBE_CONFIRM, 'CrossEngage', 'Index', 'subscribeConfirmation')
            ->assert('newsletter', $this->getAllowedLocalesPattern() . 'newsletter|newsletter')
            ->value('newsletter', 'newsletter')
            ->method('GET');

        return $this;
    }
}
