<?php

include_once __DIR__ . '/const.php';
include_once __DIR__ . '/config.php';

class erstegroup {

    /**
     * @var config
     */
    private $config = null;

    function __construct($config = null) {
        if (is_int($config)) {
            $this->config = new config(Array());
            $this->loadSettings();
        } else {
            if (is_object($config)) {
                $this->config = $config;
            }
            
            $this->loadSettings();
        }
    }

    public function getServer($canonical = false) {
        if ($canonical) {
            return ($this->config->OAUTH2['server'] == Constants::SANDBOX) ? Constants::TOKEN_TYPE_SANDBOX : Constants::TOKEN_TYPE_SANDBOX;
        } else {
            return $this->config->OAUTH2['server'];
        }
    }

    // corporate
    public function getAccounts() {

        if ($this->getAuth()) {
            return $json = $this->parseJSON($this->e_curl(
                                    $this->config->getURL(WebAPI::ACCOUNTS)))['accounts'];
        } else {
            return false;
        }
    }

    public function getTransactions($id) {
        if ($this->getAuth()) {

            return $json = $this->parseJSON($this->e_curl(
                            $this->config->getURL(WebAPI::TRANSACTIONS, $id)));
        } else {
            return false;
        }
    }

    public function clearSettings() {
        file_exists(__DIR__ . '/' . $this->getServer(true)) ? unlink(__DIR__ . '/' . $this->getServer(true)): true;
    }

    private function isAuth() {
        // DID refresh_token EXPIRE?
        if (!isset($this->config->OAUTH2['timestamp']) ||
                ( $this->config->OAUTH2['timestamp'] + $this->config->OAUTH2['refresh_length'] ) < time()
        ) {
            return OAUTH2::OAUTH2;
        } else
        // DID access_token EXPIRE?
        if (( $this->config->OAUTH2['timestamp'] + $this->config->OAUTH2['access_length'] ) < time()) {
            return OAUTH2::OAUTH2REFRESH;
        } else {
            // READY for QUERY
            return OAUTH2::OAUTH2READY;
        }
    }

    public function getAuth() {
        if (is_object($this->config)) {

            // check if authorized
            $isAuth = $this->isAuth();
            $url = $this->config->getURL($isAuth);

            // need auth
            if (($isAuth == OAUTH2::OAUTH2REFRESH) ||
                    ($isAuth == OAUTH2::OAUTH2 && empty($this->config->OAUTH2['code']))
            ) {
                // ACCESS_TOKEN none or need REFRESH 
                if($this->config->OAUTH2['mode'] == Constants::API_SILENT && $isAuth == OAUTH2::OAUTH2) { return false; }
                $json = $this->parseJSON($this->e_curl($url), true);

                if ($this->isAuth() == OAUTH2::OAUTH2REFRESH) {
                    // aquire NEW ACCESS_TOKEN with REFRESH_TOKEN
                    $this->config->updateOAUTH2($json);
                    $this->saveSettings($json);
                    return true;
                }
            } else {
                if (!empty($this->config->OAUTH2['code'])) {
                    // aquire TOKEN from CODE
                    $json = $this->e_curl($this->config->getURL(OAUTH2::OAUTH2TOKEN));
                } else {
                    // valid OAUTH2
                    return true;
                }

                if (is_int($json)) {
                    // error occured
                    switch ($json) {
                        case 400;
                            // BAD REQUEST
                            die(ERRORS::OAUTH2_400);
                        case 401:
                            // UNAUTHORIZED
                            die(ERRORS::OAUTH2_401);
                        case 500:
                            // INTERNAL SERVER ERROR
                            die(ERRORS::OAUTH2_500);
                    }
                }

                $json = $this->parseJSON($json, true);
                $this->config->updateOAUTH2($json);

                $this->saveSettings($json);
                
                if (isset ($_GET['complete'])) {
                    header('Location: ' . explode('?', $_SERVER['REQUEST_URI'])[0] );
                }
                
                return true;
            }
        }
    }

    private function parseJSON($json,
            $add = false) {
        $ret = json_decode($json, true);
        if ($add) {
            $ret['refresh_length'] = Constants::TOKEN_REFRESH;
        }
        $ret['timestamp'] = time();

        return $ret;
    }

    private function e_curl($url) {

        //die(var_dump($url));
        switch ($url['type']) {
            case Constants::POST:
                $c = curl_init($url['url']);

                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, $url['data']);
                curl_setopt($c, CURLOPT_HTTPHEADER, Array('Content-Type: application/x-www-form-urlencoded'));
                break;
            case Constants::GET :
                $c = curl_init($url['url'] . $url['data']);
                curl_setopt($c, CURLOPT_HTTPHEADER,
                        Array('web-api-key: ' . $this->config->getAPI_KEY(),
                            'authorization: Bearer ' . $this->config->OAUTH2['access_token']));
                break;
            default :
                header("Location: " . $url['url'] . $url['data']);
                exit();
        }

        curl_setopt($c, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);

        $ret = curl_exec($c);

        //die(var_dump($ret));
        switch ($http_code = curl_getinfo($c, CURLINFO_HTTP_CODE)) {
            case 200:
                return $ret;
            default:
                return $http_code;
        }
        curl_close($c);
    }

    private function loadSettings($s = null) {

        $server = (!is_null($s)) ? $s : $this->config->OAUTH2['server'];

        switch ($server) {
            case Constants::SANDBOX :
                $type = Constants::TOKEN_TYPE_SANDBOX;
                break;
            case Constants::REAL :
                $type = Constants::TOKEN_TYPE_REAL;
                break;
            default:
                $type = Constants::TOKEN_TYPE_SANDBOX;
                break;
        }

        $result = (file_exists(__DIR__ . '/' . $type)) ? unserialize(file_get_contents(__DIR__ . '/' . $type)) : null;

        if (is_array($result) && isset($result)) {

            $this->config->updateOAUTH2(
                    Array('access_token' => $result['access_token'],
                        'refresh_token' => $result['refresh_token'],
                        'timestamp' => strtotime($result['valid']),
                        'token_type' => $result['token_type'],
                        'access_length' => $result['access_length'],
                        'refresh_length' => $result['refresh_length']
            ));
        }
    }

    private function saveSettings($token) {

        $token['token_type'] = ($this->config->OAUTH2['server'] == Constants::SANDBOX) ? Constants::TOKEN_TYPE_SANDBOX : Constants::TOKEN_TYPE_REAL;

        $token['refresh_token'] = (!isset($token['refresh_token']) || is_null($token['refresh_token'])) ? $this->config->OAUTH2['refresh_token'] : $token['refresh_token'];

        $data = Array(
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'],
            'code' => $this->config->OAUTH2['code'],
            'valid' => date('Y-m-d H:i:s', $token['timestamp']),
            'token_type' => $token['token_type'],
            'access_length' => $token['expires_in'],
            'refresh_length' => Constants::TOKEN_REFRESH,
        );

        file_put_contents(__DIR__ . '/' . $token['token_type'], serialize($data));

        return true;
    }

}

?>