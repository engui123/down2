<?php
// login.php
header('Content-Type: application/json');
// Certifique-se que NADA é impresso neste arquivo ou no conecta.php antes do <?php

include 'conecta.php'; 

// 🚨 CORREÇÃO: Verifica se a conexão falhou
if (!isset($conn) || $conn->connect_error) {
    // Retorna um erro JSON e impede que o código HTML/Warning do PHP chegue ao JS
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro interno: Falha ao conectar ao banco de dados."]);
    exit();
}
// FIM CORREÇÃO

// Inicia a sessão (necessário para manter o usuário logado)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recebe e limpa os dados
    $email = trim($conn->real_escape_string($_POST['email']));
    $senha_digitada = $_POST['senha'];

    if (empty($email) || empty($senha_digitada)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "E-mail e senha são obrigatórios."]);
        $conn->close();
        exit();
    }

    // 2. Busca o usuário no banco
    $sql = "SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erro de banco de dados. Tente novamente."]);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // 3. Verifica a senha
        if (password_verify($senha_digitada, $usuario['senha'])) {
            
            // 4. Login bem-sucedido: Cria a sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            
            // 5. Determina a URL de redirecionamento usando o caminho absoluto
            $pasta_projeto = '/mercado-online/'; 
            
            $redirect_url = '';
            if ($usuario['tipo'] === 'empresa' || $usuario['tipo'] === 'admin') {
                // Redireciona para o admin.php (agora é PHP, não HTML)
                $redirect_url = "admin.php"; // 🚨 AJUSTE PARA .php
            } else {
                // Redireciona para a área do cliente (shop.php, conforme a sua estrutura)
                $redirect_url = "shop.php"; // 🚨 AJUSTE PARA shop.php
            }
            
            echo json_encode([
                "success" => true, 
                "message" => "Login realizado com sucesso!",
                "redirect" => $redirect_url 
            ]);

        } else {
            // Senha incorreta
            echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]);
        }
    } else {
        // Usuário não encontrado
        echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido."]);
}
?>