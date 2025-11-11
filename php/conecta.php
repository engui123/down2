<?php
// conecta.php - Conexão segura com MySQL
$host = "localhost";
$user = "root"; // USUÁRIO
$pass = "";  // SENHA (DEIXE VAZIO "" SE NÃO HOUVER)
$db  = "valify"; // NOME DO BANCO (Confirmado em valify.sql)

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
http_response_code(500);
    // Retorna JSON para que o JavaScript possa tratar
 die(json_encode(["success" => false, "message" => "Erro de Conexão com o Banco de Dados. Verifique o servidor MySQL e as credenciais."]));
}

$conn->set_charset("utf8mb4");
?>