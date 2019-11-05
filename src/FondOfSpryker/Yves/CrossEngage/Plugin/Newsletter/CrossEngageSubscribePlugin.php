<?php

namespace FondOfSpryker\Yves\CrossEngage\Plugin\Newsletter;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Yves\Newsletter\Dependency\Plugin\NewsletterSubscribePluginInterface;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Generated\Shared\Transfer\NewsletterResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \FondOfSpryker\Yves\CrossEngage\CrossEngageFactory getFactory()
 * @method \FondOfSpryker\Client\CrossEngage\CrossEngageClientInterface getClient()
 */
class CrossEngageSubscribePlugin extends AbstractPlugin implements NewsletterSubscribePluginInterface
{
    /**
     * @param string $email
     * @param Request $request
     * @return NewsletterResponseTransfer
     * @throws \Exception
     */
    public function subscribe(string $email, Request $request): NewsletterResponseTransfer
    {
        $mapper = $this->getFactory()
            ->createStoreTransferMapper();

        $xngTransfer = new CrossEngageTransfer();
        $xngTransfer
            ->setEmail($email)
            ->setExternalId(\sha1($email))
            ->setLanguage(\explode('_', $this->getLocale())[0])
            ->setBusinessUnit($this->getLocale())
            ->setHost($request->getSchemeAndHttpHost());

        $xngTransfer = $mapper->setEmailState($xngTransfer, CrossEngageConstants::XNG_STATE_NEW);
        $xngTransfer = $mapper->setEmailOptInSource($xngTransfer);
        $xngTransfer = $mapper->setOptInAtFor($xngTransfer, null);
        $xngTransfer = $mapper->setIp($xngTransfer, $request->getClientIp());

        $xngResponse = $this->getClient()->subscribe($xngTransfer);

        return (new NewsletterResponseTransfer())
            ->fromArray($xngResponse->toArray(), true);
    }

    /**
     * @param string $externalId
     * @return NewsletterResponseTransfer
     */
    public function confirmSubscription(string $externalId): NewsletterResponseTransfer
    {
        $xngTransfer = new CrossEngageTransfer();
        $xngTransfer->setExternalId($externalId);

        $xngResponse = $this->getClient()->confirmSubscription($xngTransfer);

        return (new NewsletterResponseTransfer())
            ->fromArray($xngResponse->toArray(), true);
    }

    /**
     * @param string $externalId
     *
     * @return NewsletterResponseTransfer
     */
    public function unsubscribe(string $externalId): NewsletterResponseTransfer
    {
        $xngTransfer = new CrossEngageTransfer();
        $xngTransfer->setExternalId($externalId);

        $xngResponse = $this->getClient()->unsubscribe($xngTransfer);

        return (new NewsletterResponseTransfer())
            ->fromArray($xngResponse->toArray(), true);
    }
}
