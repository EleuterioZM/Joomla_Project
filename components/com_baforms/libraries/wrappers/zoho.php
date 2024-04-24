<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class zoho_crm
{
    public $client_id;
    public $client_secret;
    public $scope;
    public $redirect_uri;
    public $auth;

    public function __construct($client_id = null, $client_secret = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->scope = 'ZohoCRM.users.ALL,Aaaserver.profile.read,ZohoCRM.settings.all,ZohoCRM.modules.ALL';
        $this->redirect_uri = JUri::root().'administrator/index.php?option=com_baforms&task=form.generateZohoCRMAccessToken';
        $this->db = JFactory::getDbo();
    }

    public function getAuthorizationURL()
    {
        $url = 'https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id='.$this->client_id;
        $url .= '&scope='.$this->scope.'&redirect_uri='.urlencode($this->redirect_uri).'&access_type=offline';

        return $url;
    }

    public function authenticateCode($code, $account)
    {
        $url = $account.'/oauth/v2/token';
        $data = array(
            'client_id' => $this->client_id,
            'grant_type' => 'authorization_code',
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'code' => $code
        );
        $json = json_encode($data);
        $response = $this->fetch($url, null, $data);

        return $response;
    }

    public function refreshToken()
    {
        $url = $this->auth->account.'/oauth/v2/token';
        $data = array(
            'client_id' => $this->client_id,
            'grant_type' => 'refresh_token',
            'client_secret' => $this->client_secret,
            'refresh_token' => $this->auth->refresh_token
        );
        $json = json_encode($data);
        $response = $this->fetch($url, null, $data);
        $this->auth->access_token = $response->access_token;
        $this->checkStoredAuth();
    }

    public function checkStoredAuth()
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$this->db->quote('zoho_auth'));
        $this->db->setQuery($query);
        $object = $this->db->loadObject();
        $auth = json_decode($object->key);
        if ((isset($auth->client_secret) && $auth->client_secret == $this->auth->client_secret) &&
            $auth->client_id == $this->auth->client_id && $auth->refresh_token == $this->auth->refresh_token) {
            $object->key = json_encode($this->auth);
            $this->db->updateObject('#__baforms_api', $object, 'id');
        }
    }

    public function setAuth($object)
    {
        $this->auth = $object;
    }

    public function getFields()
    {
        $fields = $this->fetchFields();
        if (isset($fields->code) && $fields->code == 'INVALID_TOKEN') {
            $this->refreshToken();
            $fields = $this->fetchFields();
        }
        $fields = $this->prepareFields($fields);
        $object = new stdClass();
        $object->auth = $this->auth;
        $object->fields = $fields;

        return $object;
    }

    public function fetchFields()
    {
        $url = $this->auth->api_domain.'/crm/v2/settings/fields?module=Leads';
        $header = array('Authorization: '.$this->auth->token_type.' '.$this->auth->access_token);
        $fields = $this->fetch($url, $header);

        return $fields;
    }

    public function prepareFields($object)
    {
        $data = array();
        foreach ($object->fields as $field) {
            if ($field->data_type == 'profileimage' || $field->data_type == 'ownerlookup' || $field->field_read_only) {
                continue;
            }
            $data[] = array(
                'api_name' => $field->api_name,
                'label' => $field->field_label,
                'required' => $field->system_mandatory
            );
        }

        return $data;
    }

    public function insertContact($contact)
    {
        $post = array('data' => array($contact));
        $data = json_encode($post);
        $response = $this->updateLead($data);
        if (isset($response->code) && $response->code == 'INVALID_TOKEN') {
            $this->refreshToken();
            $response = $this->updateLead($data);
        }
        if (isset($response->data) && isset($response->data[0]) && $response->data[0]->code == 'INVALID_DATA') {
            print_r($response);
        }
    }

    public function updateLead($data)
    {
        $url = $this->auth->api_domain.'/crm/v2/Leads';
        $header = array('Authorization: '.$this->auth->token_type.' '.$this->auth->access_token);
        $response = $this->fetch($url, $header, $data);

        return $response;
    }

    public function fetch($url, $header = null, $body = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if ($body) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        $response = json_decode($result);

        return $response;
    }
}