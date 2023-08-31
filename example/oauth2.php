<?php

include_once __DIR__.'/../src/erstegroup.php';

$erste = new erstegroup(new config(
  Array(
  'server_type' => Constants::SANDBOX,
  'mode' => Constants::API_GUI,
  'oauth2' => Array(
    // add credentials from your application on https://developers.erstegroup.com/portal/
    'api_key' => 'API KEY',
    'client_id' => 'CLIENT ID',
    'client_secret' => 'CLIENT SECRET',
    // this url have to be set in allowed urls in your application on https://developers.erstegroup.com/portal/
    'redirect_uri' => 'https://example.tdl/api/erstegroup_api/example/oauth2.php?complete',
    'code' => (isset($_GET['complete'], $_GET['code'])) ? $_GET['code']: null))
  ));

$erste->getAuth();

// display all accounts
$account = $erste->getAccounts();

if (is_array($account)) {
    foreach ($account as $ac) {
        $transactions = $erste->getTransactions($ac['id']);
        var_dump($ac);
        var_dump($transactions);
    }
} else {
    // implement own method for bad/no access tokens
    echo '<b>ACCESS FORBIDEN</b><br>';
    echo 'API TYPE: ';
    echo ($erste->getServer() == Constants::REAL) ? '<b>PRODUCTION</b>' : '<b>SANDBOX</b>';
    $erste->clearSettings();
}
?>