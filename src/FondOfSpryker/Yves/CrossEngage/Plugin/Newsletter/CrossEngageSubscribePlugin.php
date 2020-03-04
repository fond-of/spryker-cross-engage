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
     * @param  string  $email
     * @param  Request $request
     * @return NewsletterResponseTransfer
     * @throws \Exception
     */
    public function subscribe(string $email, Request $request): NewsletterResponseTransfer
    {
        $mapper = $this->getFactory()
            ->createStoreTransferMapper();

        $crossEngageTransfer = new CrossEngageTransfer();
        $crossEngageTransfer
            ->setEmail($email)
            ->setExternalId(\sha1($email))
            ->setLanguage(\explode('_', $this->getLocale())[0])
            ->setBusinessUnit($this->getLocale())
            ->setHost($request->getSchemeAndHttpHost());

        $crossEngageTransfer = $mapper->setEmailState($crossEngageTransfer, CrossEngageConstants::XNG_STATE_NEW);
        $crossEngageTransfer = $mapper->setEmailOptInSource($crossEngageTransfer);
        $crossEngageTransfer = $mapper->setOptInAtFor($crossEngageTransfer, null);
        $crossEngageTransfer = $mapper->setIp($crossEngageTransfer, $this->getCustomerIpAddress());
        $crossEngageTransfer->setUriLanguageKey($this->executeUrlLanguageKeyPlugins());

        $xngResponse = $this->getClient()->subscribe($crossEngageTransfer);

        return (new NewsletterResponseTransfer())
            ->fromArray($xngResponse->toArray(), true);
    }

    /**
     * @param  string $externalId
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

    /**
     * @return string
     */
    protected function executeUrlLanguageKeyPlugins(): string
    {
        $store = $this->getFactory()->getStore();

        foreach ($this->getFactory()->getUrlLanguageKeyPlugins() as $languageKeyPlugin) {
            $uriLanguageKey = $languageKeyPlugin->getLanguageKey($store);

            if ($uriLanguageKey !== '') {
                return $uriLanguageKey;
            }
        }

        return '';
    }

    /**
     * @return string|null
     */
    protected function getCustomerIpAddress(): ?string
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ipAddress;
    }
}
