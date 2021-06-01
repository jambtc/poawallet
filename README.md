<p align="center">
    <a href="web/css/images.logo.png" target="_blank">
        <img src="web/css/images/logo.png" height="100px">
    </a>
    <h1 align="center">Poa Wallet</h1>
    <br>
</p>


Install Poa Wallet, the Progressive Web App mobile works with several crypto tokens and blockchain wallets. With Poa Wallet, you are in control over your funds. Receive, send, store and exchange your cryptocurrency within the mobile interface.


### Poa Wallet is based on Yii2 framework


Yii 2 Basic Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![build](https://github.com/yiisoft/yii2-app-basic/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Abuild)




CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


### Folder rights
```
chgrp www-data ./web/assets
chmod g+w ./web/assets/

chgrp www-data ./runtime
chmod g+w ./runtime/


```

### Migrate database
```
./yii migrate
```
