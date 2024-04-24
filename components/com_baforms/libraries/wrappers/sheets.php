<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_baforms/libraries/google-v4/vendor/autoload.php';

class sheets
{
    private $client;
    private $range;

    public function __construct($client_id, $client_secret)
    {
        $this->client = new Google_Client();
        $scope = array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/drive',
            'https://spreadsheets.google.com/feeds');
        $this->client->setApplicationName('Balbooa Google Drive Spreadsheets');
        $this->client->addScope($scope);
        $this->client->setClientId($client_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
        $this->client->setAccessType('offline');
    }

    public function getAuthentication()
    {
        $authUrl = $this->client->createAuthUrl();

        return $authUrl;
    }

    public function createAccessToken($code)
    {
        try {
            $token = $this->client->authenticate($code);
            $accessToken = json_encode($token);
        } catch (Exception $e) {
            $accessToken = 'INVALID_TOKEN';
        }

        return $accessToken;
    }

    protected function setAccessToken($accessToken)
    {
        $this->client->setAccessToken($accessToken);
        if ($this->client->isAccessTokenExpired()) {
            $token = json_decode($accessToken);
            $this->client->refreshToken($token->refresh_token);
        }
    }

    public function getSpreadsheet($accessToken)
    {
        $sheets = array();
        try {
            $this->setAccessToken($accessToken);
            $drive = new Google_Service_Drive($this->client);
            $oauth = new Google_Service_Oauth2($this->client);
            $userinfo = $oauth->userinfo->get();
            $pageToken = null;
            $q = "mimeType='application/vnd.google-apps.spreadsheet' and trashed = false and '";
            $q .= $userinfo->email."' in writers";
            do {
                $params = array('pageToken' => $pageToken, 'spaces' => 'drive', 'q' => $q);
                $files = $drive->files->listFiles($params);
                $pageToken = $files->getNextPageToken();
                foreach ($files as $file) {
                    if ($file->mimeType == 'application/vnd.google-apps.spreadsheet') {
                        $sheet = new stdClass();
                        $sheet->id = $file->id;
                        $sheet->title = $file->name;
                        $sheets[$file->id] = $sheet;
                    }
                }
            } while ($pageToken != null);
        } catch (Exception $e) {
            
        }

        return $sheets;
    }

    public function getWorkSheets($accessToken, $spreadsheetId)
    {
        $worksheets = array();
        $this->setAccessToken($accessToken);
        $this->service = new Google_Service_Sheets($this->client);
        $spreadsheet = $this->service->spreadsheets->get($spreadsheetId);
        foreach ($spreadsheet->sheets as $sheet) {
            $obj = new stdClass();
            $obj->id = $sheet->properties->sheetId;
            $obj->title = $sheet->properties->title;
            $worksheets[$obj->id] = $obj;
        }

        return $worksheets;
    }

    public function getWorkSheetsColumns($accessToken, $spreadsheetId, $sheetId)
    {
        $array = array();
        try {
            $this->setAccessToken($accessToken);
            $this->service = new Google_Service_Sheets($this->client);
            $spreadsheet = $this->service->spreadsheets->get($spreadsheetId);
            foreach ($spreadsheet->sheets as $sheet) {
                if ($sheet->properties->sheetId == $sheetId) {
                    $this->range = $sheet->properties->title.'!A1:AAZ1';
                    $response = $this->service->spreadsheets_values->get($spreadsheetId, $this->range);
                    $values = $response->getValues();
                    $array = $values[0];
                    break;
                }
            }
        } catch (Exception $e) {
            
        }

        return $array;
    }

    public function insert($accessToken, $row, $spreadsheetId, $sheetId)
    {
        $array = $this->getWorkSheetsColumns($accessToken, $spreadsheetId, $sheetId);
        $data = array();
        try {
            foreach ($array as $value) {
                $data[] = '';
            }
            foreach ($row as $key => $value) {
                $i = array_search($key, $array);
                if ($i !== false) {
                    $data[$i] = $value;
                }
            }
            if (empty($data)) {
                return;
            }
            $body = new Google_Service_Sheets_ValueRange([
                'values' => array($data)
            ]);
            $params = array('valueInputOption' => 'USER_ENTERED', 'insertDataOption' => 'INSERT_ROWS');
            $result = $this->service->spreadsheets_values->append($spreadsheetId, $this->range, $body, $params);
        } catch (Exception $e) {
            
        }
    }
}