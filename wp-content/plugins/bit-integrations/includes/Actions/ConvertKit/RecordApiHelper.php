<?php

/**
 * Convert Kit Record Api
 */

namespace BitCode\FI\Actions\ConvertKit;

use BitCode\FI\Log\LogHandler;
use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,update, exist
 */
class RecordApiHelper
{
    private $_defaultHeader;

    private $_integrationID;

    private $_apiEndpoint;

    public function __construct($api_secret, $integId)
    {
        // wp_send_json_success($tokenDetails);
        $this->_defaultHeader = $api_secret;
        $this->_apiEndpoint = 'https://api.convertkit.com/v3';
        $this->_integrationID = $integId;
    }

    // for adding a subscriber
    public function storeOrModifyRecord($method, $formId, $data)
    {
        $queries = $this->httpBuildQuery($data);
        $insertRecordEndpoint = "{$this->_apiEndpoint}/forms/{$formId}/{$method}?{$queries}";

        return HttpHelper::post($insertRecordEndpoint, null);
    }

    // for updating subscribers data through email id.
    public function updateRecord($id, $data, $existSubscriber)
    {
        $queries = $this->httpBuildQuery($data);
        $updateRecordEndpoint = "{$this->_apiEndpoint}/subscribers/{$id}?" . $queries;

        return HttpHelper::request($updateRecordEndpoint, 'PUT', null);
    }

    // add tag to a subscriber
    public function addTagToSubscriber($email, $tags)
    {
        $queries = http_build_query([
            'api_secret' => $this->_defaultHeader,
            'email'      => $email,
        ]);

        foreach ($tags as $tagId) {
            $searchEndPoint = "{$this->_apiEndpoint}/tags/{$tagId}/subscribe?{$queries}";

            HttpHelper::post($searchEndPoint, null);
        }
    }

    public function execute($fieldValues, $fieldMap, $actions, $formId, $tags)
    {
        $fieldData = [];
        $customFields = [];

        foreach ($fieldMap as $fieldKey => $fieldPair) {
            if (!empty($fieldPair->convertKitField)) {
                if ($fieldPair->formField === 'custom' && isset($fieldPair->customValue) && !is_numeric($fieldPair->convertKitField)) {
                    $fieldData[$fieldPair->convertKitField] = Common::replaceFieldWithValue($fieldPair->customValue, $fieldValues);
                } elseif (is_numeric($fieldPair->convertKitField) && $fieldPair->formField === 'custom' && isset($fieldPair->customValue)) {
                    $customFields[] = ['field' => (int) $fieldPair->convertKitField, 'value' => Common::replaceFieldWithValue($fieldPair->customValue, $fieldValues)];
                } elseif (is_numeric($fieldPair->convertKitField)) {
                    $customFields[] = ['field' => (int) $fieldPair->convertKitField, 'value' => $fieldValues[$fieldPair->formField]];
                } else {
                    $fieldData[$fieldPair->convertKitField] = $fieldValues[$fieldPair->formField];
                }
            }
        }

        if (!empty($customFields)) {
            $fieldData['fieldValues'] = $customFields;
        }
        $convertKit = (object) $fieldData;

        $existSubscriber = $this->existSubscriber($convertKit->email);

        if (isset($existSubscriber->subscribers) && (\count($existSubscriber->subscribers)) !== 1 && !isset($existSubscriber->error)) {
            $recordApiResponse = $this->storeOrModifyRecord('subscribe', $formId, $convertKit);
            if (isset($tags) && (\count($tags)) > 0 && $recordApiResponse) {
                $this->addTagToSubscriber($convertKit->email, $tags);
            }
            $type = 'insert';
        } elseif (!isset($existSubscriber->error)) {
            if ($actions->update == 'true') {
                $this->updateRecord($existSubscriber->subscribers[0]->id, $convertKit, $existSubscriber);
                $type = 'update';
            } else {
                LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'error', __('Email address already exists in the system', 'bit-integrations'));

                wp_send_json_error(
                    [
                        'type'    => 'error',
                        'message' => __('Check your email for confirmation.', 'bit-integrations')
                    ],
                    400
                );
            }
        }

        if (isset($existSubscriber->error)) {
            LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => $type], 'error', $existSubscriber->error);
        } elseif ($recordApiResponse && isset($recordApiResponse->error)) {
            LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => $type], 'error', $recordApiResponse->error);
        } else {
            LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => $type], 'success', $recordApiResponse);
        }

        return $recordApiResponse;
    }

    private function httpBuildQuery($data)
    {
        $query = [
            'api_secret' => $this->_defaultHeader,
            'email'      => $data->email,
            'first_name' => $data->firstName,
        ];

        foreach ($data as $key => $value) {
            $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            $array_keys = array_keys($query);
            if (!(\in_array($key, $array_keys))) {
                $query['fields'][$key] = $value;
            }
        }

        return http_build_query($query);
    }

    // Check if a subscriber exists through email.
    private function existSubscriber($email)
    {
        $queries = http_build_query([
            'api_secret'    => $this->_defaultHeader,
            'email_address' => $email,
        ]);
        $searchEndPoint = "{$this->_apiEndpoint}/subscribers?{$queries}";

        return HttpHelper::get($searchEndPoint, null);
    }
}
