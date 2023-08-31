<?php

class Constants {
    
    // server
    const SANDBOX = 1;
    const REAL    = 2;
    
    // curl method
    const POST     = 1;
    const GET      = 2;
    const REDIRECT = 3;
    
    // oauth prop
    const OA_ACCESS_TOKEN  = 1;
    const OA_REFRESH_TOKEN = 2;
    
    // tokens
    const TOKEN_TYPE_SANDBOX = 'sandbox';
    const TOKEN_TYPE_REAL    = 'bearer';
    const TOKEN_REFRESH = 15552000;
    
    // this API behaviour
    const API_SILENT = 1; // for grabbing data only
    const API_GUI    = 2; // for doing login ( ex. from admin page )
    
}

class WebAPI {
    
    const SANDBOX = 'https://webapi.developers.erstegroup.com/api/csas/public/sandbox/v2/accounts';
    const REAL    = 'https://www.csas.cz/webapi/api/v2/accounts/my/accounts';
    
    const ACCOUNTS     = 'accounts';
    const TRANSACTIONS = 'transactions';
    
    const API_ACCOUNTS     = '/my/accounts';
    const API_TRANSACTIONS = '/my/accounts/%ID%/transactions';
}

class ERRORS {
    
    const OAUTH2_400 = "ERROR CODE: <b>400 - BAD REQUEST</b>";
    const OAUTH2_401 = "ERROR CODE: <b>401 - UNAUTHORIZED</b>";
    const OAUTH2_500 = "ERROR CODE: <b>500 - INTERNAL SERVER ERROR</b>";
}

class OAUTH2 {
    
    const OAUTH2        = 1;
    const OAUTH2REFRESH = 2;
    const OAUTH2READY   = 3;
    const OAUTH2TOKEN   = 4;
    
    const SANDBOX       = 'https://webapi.developers.erstegroup.com/api/csas/sandbox/v1/sandbox-idp/fl/oauth2/token';//'https://webapi.developers.erstegroup.com/api/csas/sandbox/v1/sandbox-idp/token';
    const SANDBOX_LOGIN = 'https://webapi.developers.erstegroup.com/api/csas/sandbox/v1/sandbox-idp/auth';
    const REAL          = 'https://bezpecnost.csas.cz/mep/fs/fl/oauth2/token';
    const REAL_LOGIN    = 'https://bezpecnost.csas.cz/mep/fs/fl/oauth2/auth';
    
    const POST_TOKEN = 'grant_type=authorization_code&code=%CODE%&client_id=%CLIENT_ID%&client_secret=%CLIENT_SECRET%&redirect_uri=%URI_COMPLETE%';
    const POST       = 'grant_type=authorization_code&code=%CODE%&client_id=%CLIENT_ID%&client_secret=%CLIENT_SECRET%&redirect_uri=%URI_COMPLETE%&response_type=code&access_type=offline&state=bit-oauth';
    const GET        = '?state=bit-oauth&response_type=code&client_id=%CLIENT_ID%&access_type=offline&redirect_uri=%URI_COMPLETE%&approval_prompt=force';
    const REFRESH    = 'grant_type=refresh_token&refresh_token=%TOKEN_REFRESH%&client_id=%CLIENT_ID%&client_secret=%CLIENT_SECRET%';
}