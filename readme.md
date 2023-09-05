<meta name="google-site-verification" content="UadqqAlLYTPiRK68OJmvXlt2ljnpmIX5shFHGQ8vKd0" />
Erstegroup API
================

Unofficial client for interacting with Erstegroup API

For one of mine customers I've created this lib for getting
info about payments done through his web/shop.

Implemented API for CSAS:

- [x] OAUTH2
- [x] Accounts - read only
- [x] Transactions - read only

#### PHP
Tested on PHP v 5.4
Needed - cURL

#### Usage
Download files from src folder - view [example](example/oauth2.php) for details

For login use:
``` bash
'mode' => Constants::API_GUI
```
For just work with data ( you have a method for refreshing token on another file )
``` bash
'mode' => Constants::API_SILENT
```
Needed actions from application on https://developers.erstegroup.com/portal/ :
- copy API_KEY, CLIENT_ID and CLIENT_SECRET
- set own url_redirect after OAUTH2 login 
  used identificator "complete" on line 125 in erstegroup.php

#### TODO for you
Implement own method for loadin/saving tokens ( now unsecure loading/saving into file )
Implement own method for viewing data ( now just plain var_dump )

#### Development
If you want implement something into this lib, feel free to add request.

#### License
Feel free to use this lib as you wish.
I will appreciate if you mention me in your project in some way :-)
