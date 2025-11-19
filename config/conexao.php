<?php
// Caminho: /config/conexao.php

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'cybercoa_api';
$user = 'cybercoa_api';
$pass = '@cybercoari';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Token do Mercado Pago (acesso da sua conta)
define('MP_ACCESS_TOKEN', 'APP_USR-3725334823147849-112610-ccb72a76edc39a5c0902ac0975e8c553-2069289633');

?>