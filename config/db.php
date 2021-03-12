<?php
$secrets = require __DIR__ . '/secrets.php';

return [
  'class' => 'yii\db\Connection',
  'dsn' => $secrets['db_dsn'],
  'username' => $secrets['db_username'],
  'password' => $secrets['db_password'],
  'charset' => 'utf8',
];
