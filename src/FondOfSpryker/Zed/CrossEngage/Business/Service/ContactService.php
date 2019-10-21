<?php

namespace FondOfSpryker\Zed\CrossEngage\Business\Service;

use FondOfPHP\CrossEngage\DataTransferObject\Contact as ContactTransfer;
use FondOfPHP\CrossEngage\Service\Contact;

class ContactService extends Contact
{
    /**
     * @param \FondOfPHP\CrossEngage\DataTransferObject\Contact $contact
     * @param int $linkListId
     *
     * @return void
     */
    public function linkContactToList(ContactTransfer $contact, int $linkListId): void
    {
        $params = $this->getListAndStatusParam($contact->getLists());

        $this->update(array_merge($params, [
            'id' => $contact->getId(),
            'email' => $contact->getEmail(),
            'p[' . $linkListId . ']' => $linkListId,
            'status[' . $linkListId . ']' => 1,
        ]));
    }

    /**
     * @param array $lists
     *
     * @return array
     */
    private function getListAndStatusParam(array $lists): array
    {
        $listIds = [];

        if (count($lists) <= 0) {
            return $listIds;
        }

        /** @var \FondOfPHP\CrossEngage\DataTransferObject\ContactMailingListRelation $transfer */
        foreach ($lists as $transfer) {
            $currentListId = (int)$transfer->getListId();
            $listIds['p[' . $currentListId . ']'] = $currentListId;
            $listIds['status[' . $currentListId . ']'] = (int)$transfer->getStatus();
        }

        return $listIds;
    }
}
