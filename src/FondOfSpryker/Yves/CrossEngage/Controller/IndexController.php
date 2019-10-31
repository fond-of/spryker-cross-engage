<?php

namespace FondOfSpryker\Yves\CrossEngage\Controller;

use Doctrine\Common\Annotations\AnnotationRegistry;
use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
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
     * @param  string $clientIp
     * @return CrossEngageTransfer
     *
     * @throws \Exception
     */
    protected function createCrossEngageTransfer(string $email, string $clientIp): CrossEngageTransfer
    {
        $setterStoreState = 'setEmailNewsletterStateFor' . $this->getFactory()->getStorename();
        $setterStoreOptIn = 'setOptInAtFor' . $this->getFactory()->getStorename();
        $setterIp = 'setIp' . $this->getFactory()->getStorename();

        $xngTransfer = new CrossEngageTransfer();
        $xngTransfer->setEmail($email);
        $xngTransfer->setLanguage(\explode('_', $this->getLocale())[0]);
        $xngTransfer->setBusinessUnit($this->getLocale());
        $xngTransfer->setExternalId(\sha1($email));
        $xngTransfer->$setterStoreState(CrossEngageConstants::XNG_STATE_NEW);
        $xngTransfer->$setterStoreOptIn((new \DateTime())->format(\DateTime::ATOM));
        $xngTransfer->$setterIp($clientIp);

        return $xngTransfer;
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
            $response = $this->getClient()->subscribe(
                $this->createCrossEngageTransfer(
                    $crossEngageSubscriptionForm->get('email')->getData(),
                    $request->getClientIp()
                )
            );

            return $this->redirectResponseInternal($response->getRedirectTo());
        }

        return [
            'crossEngageSubscriptionForm' => $crossEngageSubscriptionForm->createView(),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws
     */
    public function submitAction(Request $request): RedirectResponse
    {
        $crossEngageSubscriptionForm = $this->getFactory()->getCrossEngageSubscriptionForm()->handleRequest($request);

        if (!$crossEngageSubscriptionForm->isValid()) {
            return $this->redirectResponseInternal(HomePageControllerProvider::ROUTE_HOME);
        }

        $this->getClient()->subscribe(
            $this->createCrossEngageTransfer(
                $crossEngageSubscriptionForm->get('email')->getData(),
                $request->getClientIp()
            )
        );

        return $this->redirectResponseInternal(
            CrossEngageConstants::ROUTE_CROSS_ENGAGE_SUBSCRIBE, [
            'newsletter' => 'newsletter',
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function confirmSubscriptionAction(Request $request): RedirectResponse
    {
        $token = $request->get('token');

        if (!$token) {
            return $this->redirectResponseInternal(HomePageControllerProvider::ROUTE_HOME);
        }

        $this->getClient()->confirmSubscription(
            (new CrossEngageTransfer())->setExternalId($token)
        );

        return $this->redirectResponseInternal(HomePageControllerProvider::ROUTE_HOME);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function unsubscribeAction(Request $request): RedirectResponse
    {
        $token = $request->get('token');

        $this->getClient()->unsubscribe(
            (new CrossEngageTransfer())->setExternalId($token)
        );

        return $this->redirectResponseInternal(HomePageControllerProvider::ROUTE_HOME);
    }
}
