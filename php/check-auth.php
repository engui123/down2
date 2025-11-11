<?php
// php/check-auth.php
// ATENÇÃO: NADA DEVE SER IMPRESSO ANTES DE <?PHP (nem espaços em branco)

// 1. Inicia a sessão se ainda não foi iniciada. CRÍTICO.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Define a URL de login
$login_url = '/mercado-online/login.html'; // Ajuste se necessário

// 3. Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    // Redireciona imediatamente para o login
    header("Location: " . $login_url);
    exit();
}

// Se o script continuar a ser executado, o usuário está logado.
// A variável $_SESSION['usuario_tipo'] está disponível para checagens de permissão.
?>