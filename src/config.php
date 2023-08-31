<?php

include_once __DIR__ . '/const.php';

class config {
    /*
     * @var array
     */

    public $OAUTH2 = null;

    function __construct(Array $api_key = null) {
        if (!isset($api_key['oauth2'], $api_key['server_type'], $api_key['mode'])) {
            $this->OAUTH2 = Array();
        } else {
            $oauth2 = Array('api_key' => $api_key['oauth2']['api_key'],
                'redirect_uri' => $api_key['oauth2']['redirect_uri'],
                'client_id' => $api_key['oauth2']['client_id'],
                'client_secret' => $api_key['oauth2']['client_secret'],
                'code' => $api_key['oauth2']['code'],
                'server' => $api_key['server_type'],
                'mode' => $api_key['mode']
            );
            $this->OAUTH2 = $oauth2;
        }
    }

    public function updateOAUTH2($oauth2) {
        $this->OAUTH2 = array_merge($this->OAUTH2, $oauth2);
    }

    public function getAPI_KEY() {
        return $this->OAUTH2['api_key'];
    }

    public function getURL($type, $opt = null) {
        $url = null;
        $curl_type = null;
        $data = null;
        
        switch ($type) {

            case OAUTH2::OAUTH2 : {
                // expired refresh_token or no access_token
                    $url = ($this->OAUTH2['server'] == Constants::SANDBOX) ? OAUTH2::SANDBOX_LOGIN : OAUTH2::REAL_LOGIN;
                    $curl_type = (is_null($opt)) ? Constants::REDIRECT : Constants::POST;

                    $data = (is_null($opt)) ?
                            str_replace(Array('%URI_COMPLETE%', '%CLIENT_ID%'),
                                    Array(urlencode($this->OAUTH2['redirect_uri']),
                                        $this->OAUTH2['client_id']), OAUTH2::GET) :
                            str_replace(Array('%URI_COMPLETE%', '%CLIENT_ID%', '%CLIENT_SECRET%', '%CODE%'),
                                    Array($this->OAUTH2['redirect_uri'], $this->OAUTH2['client_id'],
                                        $this->OAUTH2['client_secret'], $opt), OAUTH2::POST);
                }
                
                break;
            case OAUTH2::OAUTH2REFRESH : {
                // expired access_token
                    $url = ($this->OAUTH2['server'] == Constants::SANDBOX) ? OAUTH2::SANDBOX : OAUTH2::REAL;

                    $curl_type = Constants::POST;
                    $data = str_replace(Array('%TOKEN_REFRESH%', '%CLIENT_SECRET%', '%CLIENT_ID%'), 
                                    Array($this->OAUTH2['refresh_token'], $this->OAUTH2['client_secret'], 
                                        $this->OAUTH2['client_id']), OAUTH2::REFRESH);
                }
                
                break;
            case OAUTH2::OAUTH2TOKEN : {
                // changing code for access_token
                    $url = ($this->OAUTH2['server'] == Constants::SANDBOX) ? OAUTH2::SANDBOX : OAUTH2::REAL;

                    $curl_type = Constants::POST;
                    $data = str_replace(Array('%CLIENT_SECRET%', '%CLIENT_ID%', '%CODE%', '%URI_COMPLETE%'),
                            Array($this->OAUTH2['client_secret'],
                                $this->OAUTH2['client_id'],
                                $this->OAUTH2['code'],
                                $this->OAUTH2['redirect_uri']), OAUTH2::POST_TOKEN);
                }
                
                break;
            case WebAPI::ACCOUNTS : {
                    $url = ($this->OAUTH2['server'] == Constants::SANDBOX) ? WebAPI::SANDBOX : WebAPI::REAL;

                    $url .= WebAPI::API_ACCOUNTS;
                    $curl_type = Constants::GET;
                    $data = '?sort=id&order=desc';
                }
                break;
            case WebAPI::TRANSACTIONS : {
                    if (is_null($opt)) {
                        return false;
                    }
                    $url = ($this->OAUTH2['server'] == Constants::SANDBOX) ? $url = WebAPI::SANDBOX : $url = WebAPI::REAL;

                    $url .= str_replace('%ID%', $opt, WebAPI::API_TRANSACTIONS);
                    $curl_type = Constants::GET;
                    
                    $data = '?fromDate=' . urlencode(date(DATE_ATOM, strtotime('-5 days')));
                }
                break;
        }
        return Array("url" => $url, "type" => $curl_type, "data" => $data);
    }

}

?>