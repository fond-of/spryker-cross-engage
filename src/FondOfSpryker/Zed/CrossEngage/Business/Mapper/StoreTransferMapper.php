<?php


namespace FondOfSpryker\Zed\CrossEngage\Business\Mapper;


use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use Generated\Shared\Transfer\CrossEngageTransfer;

class StoreTransferMapper
{
    /**
     * @var string
     */
    protected $storeName;

    public const GET = 'get';
    public const SET = 'set';
    public const EMAIL_STATE = 'EmailNewsletterStateFor';
    public const IP = 'Ip';
    public const EMAIL_OPT_IN_SOURCE = 'EmailOptInSource';
    public const OPT_IN_AT_FOR = 'optInAtFor';
    public const SUBSCRIBED_AT_FOR = 'subscribedAtFor';
    public const UNSUBSCRIBED_AT_FOR = 'unsubscribedAtFor';

    /**
     * @param string $storeName
     */
    public function __construct(string $storeName)
    {
        $this->storeName = $storeName;
    }

    /**
     * @return string
     */
    public function getStorename()
    {
        return $this->storeName;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getNewsletterStateMethod(string $method): string
    {
        return $method . static::EMAIL_STATE . $this->storeName;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getIpMethod(string $method): string
    {
        return $method . static::IP . $this->storeName;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getEmailOptInSourceMethod(string $method): string
    {
        return $method . static::EMAIL_OPT_IN_SOURCE . $this->storeName;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getOptInAtForMethod(string $method): string
    {
        return $method . static::OPT_IN_AT_FOR . $this->storeName;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getSubscribedAtForMethod(string $method): string
    {
        return $method . static::SUBSCRIBED_AT_FOR . $this->storeName;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getUnsubscribedAtForMethod(string $method): string
    {
        return $method . static::UNSUBSCRIBED_AT_FOR . $this->storeName;
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return string
     */
    public function getEmailState(CrossEngageTransfer $crossEngageTransfer): string
    {
        $getter = $this->getNewsletterStateMethod(static::GET);

        return $crossEngageTransfer->$getter();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param string              $state
     *
     * @return CrossEngageTransfer
     */
    protected function setEmailState(CrossEngageTransfer $crossEngageTransfer, string $state): CrossEngageTransfer
    {
        $setter = $this->getNewsletterStateMethod(static::SET);

        return $crossEngageTransfer->$setter($state);
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return string
     */
    protected function getIp(CrossEngageTransfer $crossEngageTransfer): string
    {
        $getter = $this->getIpMethod(static::GET);

        return $crossEngageTransfer->$getter();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageTransfer
     */
    protected function setIp(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer
    {
        $setter = $this->getIpMethod(static::SET);

        return $crossEngageTransfer->$setter($this->getIp($crossEngageTransfer));
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return string
     */
    protected function getEmailOptInSource(CrossEngageTransfer $crossEngageTransfer): string
    {
        $getter = $this->getEmailOptInSourceMethod(static::GET);

        return $crossEngageTransfer->$getter() ?? 'footer';
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return CrossEngageTransfer
     */
    protected function setEmailOptInSource(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer
    {
        $setter = $this->getEmailOptInSourceMethod(static::SET);

        return $crossEngageTransfer->$setter($this->getEmailOptInSource($crossEngageTransfer));
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return string
     */
    protected function getOptInAtFor(CrossEngageTransfer $crossEngageTransfer): string
    {
        $getter = $this->getOptInAtForMethod(static::GET);

        return $crossEngageTransfer->$getter();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param \DateTime|null      $dateTime
     *
     * @return CrossEngageTransfer
     *
     * @throws \Exception
     */
    protected function setOptInAtFor(CrossEngageTransfer $crossEngageTransfer, ?\DateTime $dateTime): CrossEngageTransfer
    {
        $setter = $this->getOptInAtForMethod(static::SET);

        if ($dateTime === null) {
            $dateTime = (new \DateTime())->format(\DateTime::ATOM);
        }

        return $crossEngageTransfer->$setter($dateTime);
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return CrossEngageTransfer
     */
    protected function getSubscribedAtFor(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer
    {
        $getter = $this->getSubscribedAtForMethod(static::GET);

        return $crossEngageTransfer->$getter();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param \DateTime|null      $dateTime
     *
     * @return CrossEngageTransfer
     *
     * @throws \Exception
     */
    protected function setSubscribedAtFor(CrossEngageTransfer $crossEngageTransfer, ?\DateTime $dateTime = null): CrossEngageTransfer
    {
        $setter = $this->getSubscribedAtForMethod(static::SET);

        if ($dateTime === null) {
            $dateTime = (new \DateTime())->format(\DateTime::ATOM);
        }

        return $crossEngageTransfer->$setter($dateTime);
    }

    /**
     * @param  CrossEngageTransfer $crossEngageTransfer
     * @return CrossEngageTransfer
     */
    protected function getUnsubscribedAtFor(CrossEngageTransfer $crossEngageTransfer): CrossEngageTransfer
    {
        $getter = $this->getUnsubscribedAtForMethod(static::GET);

        return $crossEngageTransfer->$getter();
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param \DateTime|null      $dateTime
     *
     * @return CrossEngageTransfer
     *
     * @throws \Exception
     */
    protected function setUnsubscribedAtFor(CrossEngageTransfer $crossEngageTransfer, ?\DateTime $dateTime = null): CrossEngageTransfer
    {
        $setter = $this->getUnsubscribedAtForMethod(static::SET);

        if ($dateTime === null) {
            $dateTime = (new \DateTime())->format(\DateTime::ATOM);
        }

        return $crossEngageTransfer->$setter($dateTime);
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     * @param $state
     *
     * @throws
     *
     * @return CrossEngageTransfer
     */
    public function updateEmailState(CrossEngageTransfer $crossEngageTransfer, $state): CrossEngageTransfer
    {
        $crossEngageTransfer = $this->setEmailState($crossEngageTransfer, $state);
        $crossEngageTransfer = $this->setIp($crossEngageTransfer);

        switch ($numericState = $this->getNumericState($crossEngageTransfer)) {
        case $numericState < 3:
            return $crossEngageTransfer = $this->setEmailOptInSource($crossEngageTransfer);

        case $numericState === 3:
            return $crossEngageTransfer = $this->setSubscribedAtFor($crossEngageTransfer);

        case $numericState < 0:
            return $crossEngageTransfer = $this->setUnsubscribedAtFor($crossEngageTransfer);

        default:
            throw new \Exception('this code should never reached! ' . __METHOD__);
        }

        return $crossEngageTransfer;
    }

    /**
     * @param CrossEngageTransfer $crossEngageTransfer
     *
     * @return int
     */
    public function getNumericState(CrossEngageTransfer $crossEngageTransfer): int
    {
        $getter = $this->getNewsletterStateMethod(static::GET);

        if (!array_key_exists($crossEngageTransfer->$getter(), CrossEngageConstants::XNG_NUMERIC_STATES)) {
            return 0;
        }

        return CrossEngageConstants::XNG_NUMERIC_STATES[$crossEngageTransfer->$getter()];
    }


}
