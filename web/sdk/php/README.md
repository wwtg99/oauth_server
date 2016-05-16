# GenoAuth SDK for PHP

#### Version 0.1.0

## Introduction
Refer https://en.wikipedia.org/wiki/OAuth

## Dependency
  - Purl
  - Guzzle

## Configuration
  - client_id: 应用ID，从GenoAuth处申请，未注册的应用也可以授权，但是access_token的有效期将大大缩短
  - client_secret: 应用密钥，从GenoAuth处申请后获取，与client_id成对
  - redirect_uri: 授权后返回的网址，注册的应用必须与注册时的根路径一致
  - scope: 需要授权的权限，默认获取用户信息
  - authorize_uri: 授权服务器的地址
  - token_uri: 授权换取服务器的地址
  - user_uri: 获取用户信息的地址，详见API

## Usage
1. 用户授权
   ```
   $uri = loginUri();  // 获取授权网址
   ```
   在登录页面或路由中直接跳转到上述网址中，用户授权后会返回`redirec_uri`指定的页面，并带回`code`
2. 换取access_token
   在返回的页面或路由中，获取`code`，并换取`access_token`
   ```
   $code = $_GET['code'];  // 获取code
   $re = getAccessToken($code);  // 换取access_token和失效时间 
   // ex {"access_token": "$2a$06$BVCAz1XZDrm9ZqJaopGDqecB9rtKz893F5YaQ9XtLwqyaehkf3KZm", "expires_in": 86400}
   // 可将token保存在用户cookie
   setcookie('access_token', $re['access_token'], time() + $re['expires_in'], '/');
   ```
3. 换取用户信息
   换取用户信息以检查是否登录
   ```
   $user = getUser($token);
   // output {'superuser': True, 'user_id': u'U000001', 'name': 'admin', 
   // 'descr': None, 'created_at': '2016-04-05 15:53:59.743394+08', 
   // 'label': 'admin', 'active': True, 'department_descr': None, 'roles': 'admin', 
   // 'department': 'Genowise', 'email': None, 'department_id': 'GW'}
   ```
4. 换取其他信息和服务
   地址和参数请查看API列表
   ```
   $re = getResource($uri, $token, $params);
   ```
