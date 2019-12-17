<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Importer;


use FondOfSpryker\Service\Newsletter\NewsletterService;
use FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClientInterface;
use FondOfSpryker\Zed\CrossEngage\Exception\ImportFileNotExistsException;
use Generated\Shared\Transfer\CrossEngageTransfer;
use Spryker\Shared\Kernel\Store;

class ActiveCampaignDataImporter implements CrossEngageImporterInterface
{
    public const NAME = 'ActiveCampaignDataImporter';
    public const DELIMITER = ';';
    public const DATE_CONVERT_FIELDS = [
        'AtFor'
    ];
    public const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @var array
     */
    protected $requiredHeader = [
        'traits.addressCountry',
        'traits.language',
        'traits.email',
        'traits.firstName',
        'traits.lastName',
        'traits.optInAtFor',
        'traits.subscribedAtFor',
        'traits.emailNewsletterStateFor',
        'traits.emailOptInSource'
    ];

    /**
     * @var array
     */
    protected $forceUpdateFields = [
        'language',
        'optInAtFor',
        'subscribedAtFor',
        'emailNewsletterStateFor',
        'emailOptInSource'
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
     * ActiveCampaignDataImporter constructor.
     * @param  \FondOfSpryker\Zed\CrossEngage\Business\Api\CrossEngageUserApiClientInterface  $apiClient
     * @param  \FondOfSpryker\Service\Newsletter\NewsletterService  $newsletterService
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
     * @param  string  $file
     * @param  string  $importPath
     * @return void
     */
    public function run(string $file): void
    {
        if ($this->validateFile($file)) {
            $row = 1;
            if (($handle = fopen($file, 'r')) !== false) {
                while (($data = fgetcsv($handle, 10000, static::DELIMITER)) !== false) {
                    if ($row > 1) {
                        $userData = $this->mapData(array_combine($this->header, $data));
                        $fetchedUserData = (new CrossEngageTransfer())->setExternalId($userData->getExternalId());
                        $response = $this->apiClient->fetchUser($fetchedUserData);
                        if (!$response) {
                            $response = $this->apiClient->createUser($userData);
                        } else {
                            $response = $this->apiClient->updateUser($this->updateFetchedUserData($fetchedUserData,
                                $userData));
                        }
                        unset($userData, $response, $fetchedUserData);
                    }
                    $row++;
                }
                fclose($handle);
            }
        }
    }

    /**
     * @param  string  $file
     * @return bool
     */
    public function validateFile(string $file): bool
    {
        $this->setHeader($file);
        if (count($this->header) !== count($this->requiredHeader)) {
            return false;
        }

        foreach ($this->header as $col) {
            foreach ($this->requiredHeader as $index => $reqCol) {
                if (strpos($col, $reqCol) !== false) {
                    unset($this->requiredHeader[$index]);
                    break;
                }
            }
        }

        return count($this->requiredHeader) === 0;
    }

    /**
     * @param  \Generated\Shared\Transfer\CrossEngageTransfer  $fetchedData
     * @param  \Generated\Shared\Transfer\CrossEngageTransfer  $importData
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
     * @param  array  $data
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
     * @param  string  $field
     * @return string
     */
    protected function createSetter(string $field): string
    {
        return $this->createMethod('set', $field);
    }

    /**
     * @param  string  $field
     * @return string
     */
    protected function createGetter(string $field): string
    {
        return $this->createMethod('get', $field);
    }

    /**
     * @param  string  $type
     * @param  string  $field
     * @return string
     */
    protected function createMethod(string $type, string $field): string
    {
        return sprintf('%s%s', strtolower($type), ucfirst(str_replace('traits.', '', $field)));
    }

    /**
     * @param  string  $file
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
     * @param  string  $field
     * @param  string  $value
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
     * @param  string  $value
     * @return false|string
     */
    protected function normalizeDate(string $value)
    {
        $split = explode(' ', $value);
        $dateExtracted = explode('.', $split[0]);
        $split[0] = implode('-', array_reverse($dateExtracted));
        return date(static::DATE_FORMAT, strtotime(implode(' ', $split)));
    }
}
