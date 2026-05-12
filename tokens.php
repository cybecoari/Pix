<?php
/**
 * ARQUIVO DE TOKENS - Cyber Coari
 * ⚠️ Configure suas credenciais localmente!
 * 
 * INSTRUÇÕES:
 * 1. Copie este arquivo para tokens.php
 * 2. Preencha com suas credenciais reais
 * 3. NUNCA commite o arquivo com credenciais reais
 */

// ==================== MERCADO PAGO ====================
// Obtenha seu token em: https://www.mercadopago.com.br/settings/developer-tools
define('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-SEU_TOKEN_AQUI');

// ==================== BANCO DE DADOS ====================
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASSWORD', 'sua_senha');
define('DB_NAME', 'seu_banco');

// ==================== CONFIGURAÇÕES ====================
define('SITE_URL', 'https://seudominio.com.br');
define('ENVIRONMENT', 'production'); // development ou production

// ==================== TELEGRAM (OPCIONAL) ====================
define('TELEGRAM_BOT_TOKEN', '');
define('TELEGRAM_CHAT_ID', '');
?>