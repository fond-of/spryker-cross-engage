<?php


namespace FondOfSpryker\Zed\CrossEngage\Business\Api;

use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use Generated\Shared\Transfer\CrossEngageBaseEventTransfer;
use Generated\Shared\Transfer\CrossEngageEventTransfer;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class CrossEngageEventApiClient
{
    /**
     * @var CrossEngageToGuzzleInterface
     */
    protected $guzzleClient;

    /**
     * @var CrossEngageConfig
     */
    protected $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface $guzzleClient
     * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig $config
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        CrossEngageToGuzzleInterface $guzzleClient,
        CrossEngageConfig $config,
        LoggerInterface $logger
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param CrossEngageEventTransfer $eventTransfer
     *
     * @return bool
     */
    public function postEvent(CrossEngageBaseEventTransfer $eventTransfer): bool
    {
        try {
            $response = $this->guzzleClient->post(
                $this->config->getCrossEngageApiUriEvents(),
                array_merge(
                    $this->config->getXngHeader(),
                    ['body' => json_encode($eventTransfer->toArray(true, true))]
                )
            );

            return $response->getStatusCode() === Response::HTTP_ACCEPTED;
        } catch (RequestException $e) {
            $this->logger->error(sprintf(
                'Can\'t send cross engage event because %s',
                $e->getMessage()
            ));
            return false;
        }
    }
}
