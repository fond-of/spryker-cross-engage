<?php


namespace FondOfSpryker\Zed\CrossEngage\Business\Mapper;


use FondOfSpryker\Shared\CrossEngage\Mapper\StoreTransferMapper;
use Generated\Shared\Transfer\CrossEngageResponseTransfer;
use Generated\Shared\Transfer\CrossEngageTransfer;

class CrossEngageResponseMapper
{
    /**
     * @var string
     */
    protected $storeName;

    /**
     * @var StoreTransferMapper
     */
    protected $storeTransferMapper;

    /**
     * @param StoreTransferMapper $storeTransferMapper
     * @param string              $storeName
     */
    public function __construct(StoreTransferMapper $storeTransferMapper, string $storeName)
    {
        $this->storeName = $storeName;
        $this->storeTransferMapper = $storeTransferMapper;
    }

    /**
     * @param array $content
     *
     * @return CrossEngageResponseTransfer
     */
    public function map(array $content): CrossEngageResponseTransfer
    {
        $response = new CrossEngageResponseTransfer();
        $response->fromArray($content, true);
        //$response = $this->mapStateForStore($response, $content);

        return $response;
    }

    /**
     * @param CrossEngageResponseTransfer $crossEngageResponseTransfer
     * @param array                       $content
     *
     * @return CrossEngageResponseTransfer|null
     */
    public function mapStateForStore(CrossEngageTransfer $crossEngageTransfer, ?array $content): CrossEngageResponseTransfer
    {
        $key = 'emailNewsletterStateFor' . $this->storeName;
        $setter = 'set' . \ucfirst($key);
        $getter = 'get' . \ucfirst($key);

        if (!method_exists($crossEngageResponseTransfer, $setter)) {
            return $crossEngageResponseTransfer;
        }

        if (!method_exists($crossEngageResponseTransfer, $getter)) {
            return $crossEngageResponseTransfer;
        }

        if (is_array($content) && !array_key_exists($key, $content)) {
            return $crossEngageResponseTransfer->$setter('new');
        }

        $numericState = $this->storeTransferMapper->getNumericState($crossEngageResponseTransfer->$getter());

        if ($numericState > 0) {
            return $crossEngageResponseTransfer;
        }

        return $crossEngageResponseTransfer;
    }
}
