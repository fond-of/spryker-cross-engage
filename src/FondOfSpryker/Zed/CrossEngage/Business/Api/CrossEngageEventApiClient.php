<?php


namespace FondOfSpryker\Zed\CrossEngage\Business\Api;

use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use Generated\Shared\Transfer\CrossEngageBaseEventTransfer;
use Generated\Shared\Transfer\CrossEngageEventTransfer;
use GuzzleHttp\Exception\RequestException;
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
     * @param CrossEngageToGuzzleInterface $guzzleClient
     * @param CrossEngageConfig            $config
     */
    public function __construct(
        CrossEngageToGuzzleInterface $guzzleClient,
        CrossEngageConfig $config
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    /**
     * @param CrossEngageEventTransfer $eventTransfer
     *
     * @return bool
     */
    public function postEvent(CrossEngageBaseEventTransfer $eventTransfer): bool
    {
        try {
            $body = json_encode($eventTransfer->toArray(true, true));

            $response = $this->guzzleClient->post(
                $this->config->getCrossEngageApiUriEvents(),
                array_merge(
                    $this->config->getXngHeader(),
                    ['body' => json_encode($eventTransfer->toArray(true, true))]
                )
            );

            return $response->getStatusCode() === Response::HTTP_ACCEPTED;
        } catch (RequestException $e) {
            return false;
        }
    }
}
