<?php

namespace FondOfSpryker\Yves\CrossEngage\Controller;

use Doctrine\Common\Annotations\AnnotationRegistry;
use FondOfSpryker\Yves\CrossEngage\Plugin\Provider\CrossEngageControllerProvider;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Yves\Kernel\Controller\AbstractController;
use SprykerShop\Yves\HomePage\Plugin\Provider\HomePageControllerProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \FondOfSpryker\Yves\CrossEngage\CrossEngageFactory getFactory()
 * @method \FondOfSpryker\Client\CrossEngage\CrossEngageClientInterface getClient()
 */
class IndexController extends AbstractController
{
    /**
     * @param string $email
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    protected function createCrossEngageTransfer(string $email): CrossEngageTransfer
    {
        return (new CrossEngageTransfer())
            ->setEmail($email)
            ->setLanguage(explode('_', $this->getLocale())[0]);
    }
    
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function formAction(Request $request): array
    {
        AnnotationRegistry::registerLoader('class_exists');

        $parentRequest = $this->getApplication()['request_stack']->getParentRequest();

        if ($parentRequest !== null) {
            $request = $parentRequest;
        }

        $crossEngageSubscriptionForm = $this->getFactory()->getCrossEngageSubscriptionForm()->handleRequest($request);

        if ($crossEngageSubscriptionForm->isValid()) {
            $this->getClient()->subscribe(
                $this->createCrossEngageTransfer($crossEngageSubscriptionForm->get('email')->getData())
            );
        }

        return [
            'crossEngageSubscriptionForm' => $crossEngageSubscriptionForm->createView(),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function submitAction(Request $request): RedirectResponse
    {
        $crossEngageSubscriptionForm = $this->getFactory()->getCrossEngageSubscriptionForm()->handleRequest($request);

        if (!$crossEngageSubscriptionForm->isValid()) {
            return $this->redirectResponseInternal(HomePageControllerProvider::ROUTE_HOME);
        }

        $this->getClient()->subscribe(
            $this->createCrossEngageTransfer($crossEngageSubscriptionForm->get('email')->getData())
        );

        return $this->redirectResponseInternal(CrossEngageControllerProvider::ROUTE_CrossEngage_SUBSCRIBE, [
            'newsletter' => 'newsletter',
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function subscribeConfirmationAction(Request $request): array
    {
        return [];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function subscribeAction(Request $request): array
    {
        return [];
    }
}
