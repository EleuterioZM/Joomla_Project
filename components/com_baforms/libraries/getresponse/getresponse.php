<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class getresponse
{
    private $api_key;
    private $list_id;
    
    public function __construct($api_key, $list_id = '')
    {
        $this->api_key = $api_key;
        $this->list_id = $list_id;
    }

    private function getResponse($endpoint, $json = null)
    {
        $url = 'https://api.getresponse.com/v3/';
        $headers = array('X-Auth-Token: api-key '.$this->api_key);
        $curl = curl_init();
        $options = array();
        if ($json) {
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $json;
        }
        $options[CURLOPT_URL] = $url.$endpoint;
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $data = json_decode($body);

        return $data;
    }

    public function getLists()
    {
        $data = $this->getResponse('campaigns');
        $response = new stdClass();
        $response->success = is_array($data);
        if ($response->success) {
            $response->lists = $data;
        }

        return $response;
    }

    public  function getFieldObject($title, $key)
    {
        $obj = new stdClass();
        $obj->title = $title;
        $obj->key = $key;

        return $obj;
    }

    public function getFields()
    {
        $data = $this->getResponse('custom-fields');
        $response = new stdClass();
        $response->success = is_array($data);
        if ($response->success) {
            $response->fields = array();
            $response->fields['name'] = $this->getFieldObject('Name', 'name');
            $response->fields['email'] = $this->getFieldObject('Email', 'email');
            foreach ($data as $field) {
                $field = $this->getFieldObject($field->name, $field->customFieldId);
                $response->fields[$field->key] = $field;
            }
        }

        return $response;
    }

    public function addSubscriber($name, $email, $custom)
    {
        $data = array(
            "name" => $name,
            "email" => $email,
            "dayOfCycle" => "0",
            "campaign" => ["campaignId" => $this->list_id],
            "customFieldValues" => $custom
        );
        $json = json_encode($data);
        $response = $this->getResponse('contacts', $json);
    }
}