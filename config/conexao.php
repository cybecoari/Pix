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
define('MP_ACCESS_TOKEN', 'seu_token_aqui');

?>