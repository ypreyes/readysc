<?php

/**
 * ZohoRecruit Record Api
 */

namespace BitCode\FI\Actions\MailPoet;

use Exception;
use BitCode\FI\Log\LogHandler;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
    private $_integrationID;

    private static $mailPoet_api;

    public function __construct($integId)
    {
        $this->_integrationID = $integId;
        static::$mailPoet_api = \MailPoet\API\API::MP('v1');
    }

    public function insertRecord($subscriber, $lists)
    {
        try {
            // try to find if user is already a subscriber
            $existing_subscriber = static::$mailPoet_api->getSubscriber($subscriber['email']);

            if (!$existing_subscriber) {
                return static::addSubscriber($subscriber, $lists);
            }

            return static::addSubscribeToLists($existing_subscriber['id'], $lists);
        } catch (\MailPoet\API\MP\v1\APIException $e) {
            if ($e->getCode() == 4) {
                // Handle the case where the subscriber doesn't exist
                return static::addSubscriber($subscriber, $lists);
            }

            return [
                'success' => false,
                'code'    => $e->getCode(),
                'message' => $e->getMessage()
            ];
        } catch (Exception $e) {
            // Handle other unexpected exceptions
            return [
                'success' => false,
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    public function execute($fieldValues, $fieldMap, $lists)
    {
        if (!class_exists(\MailPoet\API\API::class)) {
            return;
        }
        $fieldData = [];

        foreach ($fieldMap as $fieldKey => $fieldPair) {
            if (!empty($fieldPair->mailPoetField)) {
                if ($fieldPair->formField == 'custom' && isset($fieldPair->customValue)) {
                    $fieldData[$fieldPair->mailPoetField] = $fieldPair->customValue;
                } else {
                    $fieldData[$fieldPair->mailPoetField] = $fieldValues[$fieldPair->formField];
                }
            }
        }

        $recordApiResponse = $this->insertRecord($fieldData, $lists);
        if ($recordApiResponse['success']) {
            LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'success', $recordApiResponse);
        } else {
            LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'error', $recordApiResponse);
        }

        return $recordApiResponse;
    }

    private static function addSubscriber($subscriber, $lists)
    {
        try {
            $subscriber = static::$mailPoet_api->addSubscriber($subscriber, $lists);

            return [
                'success' => true,
                'id'      => $subscriber['id'],
            ];
        } catch (\MailPoet\API\MP\v1\APIException $e) {
            return [
                'success' => false,
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    private static function addSubscribeToLists($subscriber_id, $lists)
    {
        try {
            $subscriber = static::$mailPoet_api->subscribeToLists($subscriber_id, $lists);

            return [
                'success' => true,
                'id'      => $subscriber['id'],
            ];
        } catch (\MailPoet\API\MP\v1\APIException $e) {
            return [
                'success' => false,
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }
}
