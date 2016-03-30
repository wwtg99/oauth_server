# OAuth Server

### Usage
1. Create database by files in sql in Postgresql
2. Start service and change app_config.php
3. Request
    - Request `/authorize` with redirect_uri and client_id (optional) to get code
    - Request `/token` with code and client_id, client_secret, code and redirect_uri to get access_token
    Or,
    - Enable `use TOpenAuth` in User, use /oauth/login to login in Flight2wwu
