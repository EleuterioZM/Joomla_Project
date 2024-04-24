<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class activecampaign
{
    private $account;
    private $key;
    private $headers;
    private $body;
    
    public function __construct($account, $key)
    {
        $this->account = $account;
        $this->key = $key;
        $this->headers = array('Api-Token: '.$key);
    }

    private function execCurl($endpoint, $post = null)
    {
        $curl = curl_init();
        $options = array();
        if ($post) {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = json_encode($post);
        }
        $options[CURLOPT_URL] = $this->account.'/api/3/'.$endpoint;
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $this->headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $this->body = curl_exec($curl);
        $info = curl_getinfo($curl);
        $response = new stdClass();
        $response->success = ($info['http_code'] == 200 || $info['http_code'] == 201);

        return $response;
    }

    public function getLists()
    {
        $response = $this->execCurl('lists');
        if ($response->success) {
            $data = json_decode($this->body);
            $response->lists = new stdClass();
            foreach ($data->lists as $value) {
                $response->lists->{$value->id} = $value->name;
            }
        }

        return $response;
    }

    public function addContact($contact, $listId)
    {
        $array = array('contact' => $contact);
        $response = $this->execCurl('contacts', $array);
        if ($response->success) {
            $data = json_decode($this->body);
            $contactList = new stdClass();
            $contactList->list = $listId;
            $contactList->contact = $data->contact->id;
            $contactList->status = 1;
            $array = array('contactList' => $contactList);
            $this->execCurl('contactLists', $array);
        }
    }
}