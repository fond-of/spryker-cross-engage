<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Api;

use FondOfSpryker\Shared\CrossEngage\CrossEngageConstants;
use FondOfSpryker\Zed\CrossEngage\Business\Mapper\CrossEngageResponseMapper;
use FondOfSpryker\Zed\CrossEngage\CrossEngageConfig;
use FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

class CrossEngageApiClient
{
    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface
     */
    protected $guzzleClient;

    /**
     * @var \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected $storeName;
    /**
     * @var CrossEngageResponseMapper
     */
    private $responseMapper;

    /**
     * @param \FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle\CrossEngageToGuzzleInterface $guzzleClient
     * @param \FondOfSpryker\Zed\CrossEngage\CrossEngageConfig $config
     * @param \FondOfSpryker\Zed\CrossEngage\Business\Mapper\CrossEngageResponseMapper $responseMapper
     */
    public function __construct(
        CrossEngageToGuzzleInterface $guzzleClient,
        CrossEngageConfig $config,
        CrossEngageResponseMapper $responseMapper
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
        $this->responseMapper = $responseMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $transfer
     * @param array $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer|null
     */
    public function fetchUser(CrossEngageTransfer $transfer, array $options = []): ?CrossEngageResponseTransfer
    {
        try {
            $response = $this->guzzleClient->get(
                $this->config->getCrossEngageApiUriFetchUser($transfer->getEmail()),
                array_merge($this->getXngHeader(), $this->getXngRequestOptions(), $options)
            );

            $contentArray = json_decode($response->getBody()->getContents(), true);
            $crossEngageResponseTransfer = $this->responseMapper->map($contentArray);

            return $crossEngageResponseTransfer;
        } catch (RequestException $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $transfer
     * @param array $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer
     */
    public function createUser(CrossEngageTransfer $transfer, array $options = []): CrossEngageResponseTransfer
    {
        $body = json_encode($transfer->toArray(false, true));
        $crossEngageResponseTransfer = new CrossEngageResponseTransfer();

        try {
            $response = $this->guzzleClient->put(
                $this->config->getCrossEngageApiUriCreateUser($transfer->getEmail()),
                array_merge($this->getXngHeader(), $this->getXngRequestOptions(), $options, ['body' => $body])
            );

            $contentArray = json_decode($response->getBody()->getContents(), true);
            $crossEngageResponseTransfer = $this->responseMapper->map($contentArray);
            $crossEngageResponseTransfer->setStatus(CrossEngageConstants::XNG_INTERNAL_STATE_CREATED);

            return $crossEngageResponseTransfer;
        } catch (RequestException $e) {
            $crossEngageResponseTransfer->setStatus(sprintf(CrossEngageConstants::XNG_INTERNAL_STATE_CREATED_FAILED, __METHOD__));
        }

        return $crossEngageResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $transfer
     * @param array $options
     *
     * @return \Generated\Shared\Transfer\CrossEngageResponseTransfer
     */
    public function updateUser(CrossEngageTransfer $transfer, array $options = []): CrossEngageResponseTransfer
    {
        return $this->createUser($transfer, $options);
    }

    /**
     * @return array
     */
    protected function getXngHeader(): array
    {
        return [
            'headers' => [
                CrossEngageConstants::XNG_HEADER_FIELD_CONTENT_TYPE => 'application/json',
                CrossEngageConstants::XNG_HEADER_FIELD_API_VERSION => 1,
                CrossEngageConstants::XNG_HEADER_FIELD_AUTH_TOKEN => $this->config->getCrossEngageApiKey(),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getXngRequestOptions(): array
    {
        return [
            'request.options' => [
                'exceptions' => false,
            ],
        ];
    }
}
