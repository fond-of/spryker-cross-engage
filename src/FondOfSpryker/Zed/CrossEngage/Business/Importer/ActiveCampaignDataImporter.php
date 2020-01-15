<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Importer;

use Exception;
use FondOfSpryker\Service\Newsletter\NewsletterService;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClientInterface;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Log\LoggerTrait;

class ActiveCampaignDataImporter implements CrossEngageImporterInterface
{
    use LoggerTrait;

    public const NAME = 'ActiveCampaignDataImporter';
    public const DELIMITER = ';';
    public const DATE_CONVERT_FIELDS = [
        'AtFor',
    ];
    public const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @var array
     */
    protected $requiredHeaderSubscribe = [
        'traits.businessUnit',
        'traits.addressCountry',
        'traits.language',
        'traits.email',
        'traits.firstName',
        'traits.lastName',
        'traits.optInAtFor',
        'traits.subscribedAtFor',
        'traits.emailNewsletterStateFor',
        'traits.emailOptInSource',
        'traits.ip',
    ];

    /**
     * @var array
     */
    protected $requiredHeaderUnsubscribe = [
        'traits.businessUnit',
        'traits.addressCountry',
        'traits.language',
        'traits.email',
        'traits.firstName',
        'traits.lastName',
        'traits.optInAtFor',
        'traits.subscribedAtFor',
        'traits.unsubscribedAtFor',
        'traits.emailNewsletterStateFor',
        'traits.emailOptInSource',
        'traits.ip',
    ];

    /**
     * @var array
     */
    protected $forceUpdateFields = [
        'language',
        'optInAtFor',
        'subscribedAtFor',
        'emailNewsletterStateFor',
        'emailOptInSource',
        'email',
        'ip',
    ];

    /**
     * @var \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClientInterface
     */
    protected $apiClient;

    /**
     * @var \FondOfSpryker\Service\Newsletter\NewsletterService
     */
    protected $newsletterService;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * ActiveCampaignDataImporter constructor.
     *
     * @param \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClientInterface $apiClient
     * @param \FondOfSpryker\Service\Newsletter\NewsletterService $newsletterService
     */
    public function __construct(CrossEngageUserApiClientInterface $apiClient, NewsletterService $newsletterService)
    {
        $this->apiClient = $apiClient;
        $this->newsletterService = $newsletterService;
    }

    /**
     * @var array
     */
    protected $header;

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param string $file
     *
     * @return void
     */
    public function run(string $file): void
    {
        if ($this->validateFile($file, $this->requiredHeaderSubscribe) || $this->validateFile(
            $file,
            $this->requiredHeaderUnsubscribe
        )) {
            $row = 1;
            $headerCount = count($this->header);
            if (($handle = fopen($file, 'r')) !== false) {
                while (($data = fgetcsv($handle, 10000, static::DELIMITER)) !== false) {
                    if ($row > 1 && count($data) === $headerCount) {
                        try {
                            $userData = $this->mapData(array_combine($this->header, $data));
                            $fetchedUserData = (new CrossEngageTransfer())->setExternalId($userData->getExternalId());
                            $response = $this->apiClient->fetchUser($fetchedUserData);
                            if (!$response) {
                                $response = $this->apiClient->createUser($userData);
                                $state = 'created!';
                            } else {
                                $response = $this->apiClient->updateUser($this->updateFetchedUserData(
                                    $fetchedUserData,
                                    $userData
                                ));
                                $state = 'updated!';
                            }
                            $this->log(
                                sprintf('%s %s: %s', $userData->getEmail(), $state, $response->getStatus()),
                                $response->toArray()
                            );
                            unset($userData, $response, $fetchedUserData);
                        } catch (Exception $exception) {
                            $state = 'error';
                            $this->log(
                                sprintf('%s %s: %s', $userData->getEmail(), $state, $exception->getCode()),
                                ['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()],
                                $state
                            );
                        }
                    }
                    if (count($data) !== $headerCount) {
                        $this->log(sprintf('Skip row %d data: %s', $row, json_encode($data)));
                    }
                    $row++;
                }
                $this->log(sprintf('Import (%s) done. Imported items: %d', $file, $row));
                fclose($handle);
            }
        }
    }

    /**
     * @param string $file
     * @param array $requiredHeader
     *
     * @return bool
     */
    public function validateFile(string $file, array $requiredHeader): bool
    {
        $this->setHeader($file);
        if (count($this->header) !== count($requiredHeader)) {
            return false;
        }

        foreach ($this->header as $col) {
            foreach ($requiredHeader as $index => $reqCol) {
                if (strpos($col, $reqCol) !== false) {
                    unset($requiredHeader[$index]);
                    break;
                }
            }
        }

        return count($requiredHeader) === 0;
    }

    /**
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $fetchedData
     * @param \Generated\Shared\Transfer\CrossEngageTransfer $importData
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    protected function updateFetchedUserData(
        CrossEngageTransfer $fetchedData,
        CrossEngageTransfer $importData
    ): CrossEngageTransfer {
        foreach ($importData->toArray(false, true) as $field => $value) {
            foreach ($this->forceUpdateFields as $forcedField) {
                if (strpos($field, $forcedField) !== false && $value !== null) {
                    $fetchedData->{$this->createSetter($field)}($value);
                    break;
                }
            }
        }

        return $fetchedData;
    }

    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\CrossEngageTransfer
     */
    protected function mapData(array $data): CrossEngageTransfer
    {
        $xng = new CrossEngageTransfer();
        $locales = Store::getInstance()->getLocales();
        foreach ($data as $field => $value) {
            $method = $this->createSetter($field);
            $value = $this->convertData($field, $value);
            if (method_exists($xng, $method)) {
                $xng->{$method}($value);
            }
            if ($field === 'traits.language' && array_key_exists($value, $locales)) {
                $xng->setBusinessUnit($locales[$value]);
            }
        }
        $xng->setExternalId($this->newsletterService->getHash($xng->getEmail()));

        return $xng;
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected function createSetter(string $field): string
    {
        return $this->createMethod('set', $field);
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected function createGetter(string $field): string
    {
        return $this->createMethod('get', $field);
    }

    /**
     * @param string $type
     * @param string $field
     *
     * @return string
     */
    protected function createMethod(string $type, string $field): string
    {
        return sprintf('%s%s', strtolower($type), ucfirst(str_replace('traits.', '', $field)));
    }

    /**
     * @param string $file
     *
     * @return void
     */
    protected function setHeader(string $file): void
    {
        $this->header = null;
        if (($handle = fopen($file, 'r')) !== false) {
            $this->header = fgetcsv($handle, 10000, static::DELIMITER);
            fclose($handle);
        }
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @return string|null
     */
    protected function convertData(string $field, string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        foreach (self::DATE_CONVERT_FIELDS as $fieldPart) {
            if (strpos($field, $fieldPart) !== false) {
                return $this->normalizeDate($value);
            }
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return false|string
     */
    protected function normalizeDate(string $value)
    {
        $split = explode(' ', $value);
        $dateExtracted = explode('.', $split[0]);
        $split[0] = implode('-', array_reverse($dateExtracted));
        return date(static::DATE_FORMAT, strtotime(implode(' ', $split)));
    }

    /**
     * @param string $message
     * @param array $context
     * @param string $type
     *
     * @return void
     */
    protected function log(string $message, array $context = [], string $type = 'info'): void
    {
        echo $message . PHP_EOL;
        $this->getLogger()->{$type}($message, $context);
    }
}
