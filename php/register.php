<?php
require "conecta.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome  = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $tipo  = strtolower(trim($_POST["tipo"] ?? "cliente"));

    if (empty($nome) || empty($email) || empty($senha)) {
        header("Location: ../register.html?erro=Campos obrigatórios!");
        exit;
    }

   
    $tiposValidos = ['cliente', 'empresa', 'admin'];
    if (!in_array($tipo, $tiposValidos)) {
        $tipo = 'cliente'; 
    }

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../register.html?erro=Email já cadastrado!");
        exit;
    }

    $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $email, $hashSenha, $tipo);

    if ($stmt->execute()) {
        header("Location: ../login.html?sucesso=Cadastro realizado com sucesso!");
        exit;
    } else {
      
        header("Location: ../register.html?erro=Erro no cadastro: " . urlencode($stmt->error));
        exit;
    }
}
?>