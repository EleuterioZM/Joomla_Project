<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class BaformsControllerForm extends JControllerForm
{
    public function refreshGoogleFonts()
    {
        $model = $this->getModel();
        $model->refreshGoogleFonts();
        exit;
    }

    public function getZohoAuthURL()
    {
        $client_id = $this->input->get('client_id', '', 'string');
        include JPATH_ROOT.'/components/com_baforms/libraries/wrappers/zoho.php';
        $zoho = new zoho_crm();
        $redirect_uri = urlencode($zoho->redirect_uri);
        $url = 'https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id='.$client_id;
        $url .= '&scope='.$zoho->scope.'&redirect_uri='.$redirect_uri.'&access_type=offline';
        print_r($url);exit;
    }

    public function generateZohoCRMAccessToken()
    {
        $code = $this->input->get('code', '', 'string');
        $account = $this->input->get('accounts-server', '', 'string');
        $model = $this->getModel();
        $model->generateZohoCRMAccessToken($code, $account);
        exit;
    }

    public function authenticateZoho()
    {
        $code = $this->input->get('code', '', 'string');
        $account = $this->input->get('account', '', 'string');
        $client_id = $this->input->get('client_id', '', 'string');
        $client_secret = $this->input->get('client_secret', '', 'string');
        include JPATH_ROOT.'/components/com_baforms/libraries/wrappers/zoho.php';
        $zoho = new zoho_crm($client_id, $client_secret);
        $object = $zoho->authenticateCode($code, $account);
        $object->client_id = $client_id;
        $object->client_secret = $client_secret;
        $object->account = $account;
        $str = json_encode($object);
        echo $str;exit;
    }

    public function getZohoFields()
    {
        $post = $this->input->post->getArray(array());
        $auth = (object)$post;
        include JPATH_ROOT.'/components/com_baforms/libraries/wrappers/zoho.php';
        $zoho = new zoho_crm($auth->client_id, $auth->client_secret);
        $zoho->setAuth($auth);
        $data = $zoho->getFields();
        $str = json_encode($data);
        print_r($str);exit;
    }

    public function getAcymailingFields()
    {
        $fields = baformsHelper::getAcymailingFields();
        $str = json_encode($fields);
        print_r($str);exit;
    }

    public function getGoogleAuth()
    {
        $input = JFactory::getApplication()->input;
        $client_id = $input->get('client_id', '', 'string');
        $model = $this->getModel();
        $scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/drive';
        $auth = $model->getGoogleAuth($client_id, $scope);
        print_r($auth);exit;
    }

    public function getActivecampaignLists()
    {
        $input = JFactory::getApplication()->input;
        $api_key = $input->get('api_key', '', 'string');
        $account = $input->get('account', '', 'string');
        require_once JPATH_ROOT.'/components/com_baforms/libraries/activecampaign/activecampaign.php';
        $activecampaign = new activecampaign($account, $api_key);
        $data = $activecampaign->getLists();
        $str = json_encode($data);
        echo $str;exit;
    }

    public function getCampaignLists()
    {
        $input = JFactory::getApplication()->input;
        $api_key = $input->get('api_key', '', 'string');
        $client_id = $input->get('client_id', '', 'string');
        require_once JPATH_ROOT.'/components/com_baforms/libraries/campaign-monitor/campaign.php';
        $campaign = new campaign($api_key, $client_id);
        $data = $campaign->getLists();
        $str = json_encode($data);
        echo $str;exit;
    }

    public function getCampaignFields()
    {
        $input = JFactory::getApplication()->input;
        $api_key = $input->get('api_key', '', 'string');
        $client_id = $input->get('client_id', '', 'string');
        $list_id = $input->get('list_id', '', 'string');
        require_once JPATH_ROOT.'/components/com_baforms/libraries/campaign-monitor/campaign.php';
        $campaign = new campaign($api_key, $client_id, $list_id);
        $data = $campaign->getFields();
        $str = json_encode($data);
        echo $str;exit;
    }

    public function getResponseLists()
    {
        $input = JFactory::getApplication()->input;
        $api_key = $input->get('api_key', '', 'string');
        require_once JPATH_ROOT.'/components/com_baforms/libraries/getresponse/getresponse.php';
        $getresponse = new getresponse($api_key);
        $data = $getresponse->getLists();
        $str = json_encode($data);
        echo $str;exit;
    }

    public function getResponseFields()
    {
        $input = JFactory::getApplication()->input;
        $api_key = $input->get('api_key', '', 'string');
        $list_id = $input->get('list_id', '', 'string');
        require_once JPATH_ROOT.'/components/com_baforms/libraries/getresponse/getresponse.php';
        $getresponse = new getresponse($api_key, $list_id);
        $data = $getresponse->getFields();
        $str = json_encode($data);
        echo $str;exit;
    }

    public function getFormsTemplate()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $model = $this->getModel();
        $data = $model->getFormsTemplate($id);
        echo $data;exit;
    }

    public function createTemplate()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $group = $input->get('group', '', 'string');
        $model = $this->getModel();
        $model->createTemplate($id, $group);
        exit();
    }

    public function installTemplate()
    {
        $model = $this->getModel();
        $model->installTemplate();
    }

    public function saveIntegration()
    {
        baformsHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $data = $input->get('obj', '', 'raw');
        $obj = json_decode($data);
        $model = $this->getModel();
        $model->saveIntegration($id, $obj);
        exit();
    }

    public function checkState()
    {
        $str = baformsHelper::checkFormsActivation();
        echo $str;exit;
    }

    public function getRecaptchaData()
    {
        $model = $this->getModel();
        $data = $model->getRecaptchaData();
        header('Content-Type: text/javascript');
        echo 'var recaptchaData = '.$data;
        exit();
    }

    public function formsSave()
    {
        baformsHelper::checkUserEditLevel();
        $data = file_get_contents('php://input');
        $obj = json_decode($data);
        $model = $this->getModel();
        $model->formsSave($obj);
    }

    public function formsAjaxSave()
    {
        baformsHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $data = $input->get('obj', '', 'raw');
        $obj = json_decode($data);
        $model = $this->getModel();
        $model->formsSave($obj);
    }

    public function getFormShortCodes()
    {
        $model = $this->getModel();
        $obj = $model->getFormShortCodes();
        $formShortCodes = json_encode($obj);
        header('Content-Type: text/javascript');
        echo 'var formShortCodes = '.$formShortCodes;
        exit();
    }

    public function getFormOptions()
    {
        $model = $this->getModel();
        $obj = $model->getFormOptions();
        $formOptions = json_encode($obj);
        header('Content-Type: text/javascript');
        echo 'var formOptions = '.$formOptions;
        exit();
    }

    public function edit($key = null, $urlVar = null )
    {
        if (!JFactory::getUser()->authorise('core.edit', 'com_baforms')) {
            $this->setRedirect('index.php?option=com_baforms', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            return false;
        }
        $cid = $this->input->post->get('cid', array(), 'array');
        if (empty($cid)) {
            $cid[0] = $this->input->get('id');
        }
        $url = 'index.php?option=com_baforms&view=form&id='.$cid[0];
        $this->setRedirect($url);
    }

    public function createForm()
    {
        $input = JFactory::getApplication()->input;
        $title = $input->get('title', '', 'string');
        $model = $this->getModel();
        $id = $model->createForm($title);
        echo $id;
        exit();
    }

    public function getWorkSheetsColumns()
    {
        $model = $this->getModel();
        $input = JFactory::getApplication()->input;
        $client_id = $input->get('client_id', '', 'string');
        $client_secret = $input->get('client_secret', '', 'string');
        $accessToken = $input->get('accessToken', '', 'raw');
        $spreadsheet = $input->get('spreadsheet', '', 'raw');
        $worksheet = $input->get('worksheet', '', 'raw');
        $columns = $model->getWorkSheetsColumns($accessToken, $spreadsheet, $worksheet, $client_id, $client_secret);
        echo $columns;
        exit;
    }

    public function getWorkSheets()
    {
        $model = $this->getModel();
        $input = JFactory::getApplication()->input;
        $client_id = $input->get('client_id', '', 'string');
        $client_secret = $input->get('client_secret', '', 'string');
        $accessToken = $input->get('accessToken', '', 'raw');
        $spreadsheet = $input->get('spreadsheet', '', 'raw');
        $sheets = $model->getWorkSheets($accessToken, $spreadsheet, $client_id, $client_secret);
        echo $sheets;
        exit;
    }

    public function getSpreadSheets()
    {
        $input = JFactory::getApplication()->input;
        $client_id = $input->get('client_id', '', 'string');
        $client_secret = $input->get('client_secret', '', 'string');
        $token = $input->get('token', '', 'string');
        $model = $this->getModel();
        $sheets = $model->getSpreadSheets($client_id, $client_secret, $token);
        echo $sheets;
        exit;
    }

    public function createSheetsToken()
    {
        $input = JFactory::getApplication()->input;
        $client_id = $input->get('client_id', '', 'string');
        $client_secret = $input->get('client_secret', '', 'string');
        $code = $input->get('code', '', 'string');
        $model = $this->getModel();
        $sheets = $model->createSheetsToken($client_id, $client_secret, $code);
        echo $sheets;
        exit;
    }

    public function createDriveToken()
    {
        $input = JFactory::getApplication()->input;
        $client_id = $input->get('client_id', '', 'string');
        $client_secret = $input->get('client_secret', '', 'string');
        $code = $input->get('code', '', 'string');
        $model = $this->getModel();
        $drive = $model->createDriveToken($client_id, $client_secret, $code);
        echo $drive;
        exit;
    }

    public function getDriveFolders()
    {
        $input = JFactory::getApplication()->input;
        $client_id = $input->get('client_id', '', 'string');
        $client_secret = $input->get('client_secret', '', 'string');
        $token = $input->get('token', '', 'string');
        $model = $this->getModel();
        $sheets = $model->getDriveFolders($client_id, $client_secret, $token);
        echo $sheets;
        exit;
    }

    public function getSession()
    {
        $session = JFactory::getSession();
        echo new JResponseJson($session->getState());
        exit;
    }

    public function connectMailChimp()
    {
        $input = JFactory::getApplication()->input;
        $apikey = $input->get('api_key', '', 'string');
        $host = substr($apikey, strpos($apikey, '-') + 1);
        $auth = base64_encode('user:'.$apikey);
        $data = array('apikey' => $apikey);
        $json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://'.$host.'.api.mailchimp.com/3.0/lists');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.$auth));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        if (!$result) {
            $response = '0';
        } else {
            $response = $result;
        }
        print_r($response);exit;
    }

    public function getMailChimpFields()
    {
        $input = JFactory::getApplication()->input;
        $apikey = $input->get('api_key', '', 'string');
        $listId = $input->get('list_id', '', 'string');
        $host = substr($apikey, strpos($apikey, '-') + 1);
        $auth = base64_encode('user:'.$apikey);
        $data = array('apikey' => $apikey);
        $json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://'.$host.'.api.mailchimp.com/3.0/lists/'.$listId.'/merge-fields?count=100');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '. $auth));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        print_r($result);exit;
    }
}