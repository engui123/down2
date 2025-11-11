<?php
// cadastro.php
header('Content-Type: application/json');
include 'conecta.php'; // Inclui o arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recebe, limpa e valida dados básicos e novos campos
    $nome = trim($conn->real_escape_string($_POST['nome']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $senha_pura = $_POST['senha'];
    $tipo = $_POST['tipo']; // Novo campo
    $cnpj = null;

    // 1.1. Validação de campos obrigatórios
    if (empty($nome) || empty($email) || empty($senha_pura) || empty($tipo)) {
        echo json_encode(["success" => false, "message" => "Todos os campos são obrigatórios."]);
        $conn->close();
        exit();
    }
    
    // 1.2. Validação condicional do CNPJ
    if ($tipo === 'empresa') {
        $cnpj = trim($_POST['cnpj']); // Pega o CNPJ
        
        // Verifica se o CNPJ foi fornecido e é numérico
        if (empty($cnpj) || !is_numeric($cnpj) || strlen($cnpj) !== 14) {
            echo json_encode(["success" => false, "message" => "CNPJ inválido ou obrigatório para o tipo Empresa. (Apenas 14 números)"]);
            $conn->close();
            exit();
        }
        // Limpar o CNPJ (deixando apenas números)
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj); 
    }

    // 2. Verifica duplicidade de E-mail e Nome
    $sql_check = "SELECT nome, email FROM usuarios WHERE nome = ? OR email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $nome, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // ... (Lógica de verificação de duplicidade de nome e email, que já estava correta) ...
        $found_email_duplicate = false;
        $found_nome_duplicate = false;
        while ($row = $result_check->fetch_assoc()) {
            if (strtolower($row['email']) === strtolower($email)) {
                $found_email_duplicate = true;
            }
            // OBS: A duplicidade de NOME pode ser aceita se a intenção for permitir nomes repetidos.
            // Se o nome deve ser único, deixe esta verificação:
            if (strtolower($row['nome']) === strtolower($nome)) {
                $found_nome_duplicate = true;
            }
        }
        
        if ($found_email_duplicate) {
            echo json_encode(["success" => false, "message" => "Opa! Já tem esse gmail cadastrado."]);
        } elseif ($found_nome_duplicate) {
            echo json_encode(["success" => false, "message" => "Já tem um usuario com esse nome."]);
        }
        $stmt_check->close();
        $conn->close();
        exit();
    }
    
    // 3. Cadastra o novo usuário (incluindo o CNPJ, se for o caso)
    $senha_hashed = password_hash($senha_pura, PASSWORD_DEFAULT);
    // Aqui incluímos o campo CNPJ (ou NULL) na inserção. 
    // Você PRECISARÁ ADICIONAR a coluna `cnpj` à sua tabela `usuarios`.
    
    // Supondo que você adicionou a coluna 'cnpj' à sua tabela 'usuarios'
    $sql_insert = "INSERT INTO usuarios (nome, email, senha, tipo, cnpj) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    
    // 's' para string, 's' para email, 's' para senha, 's' para tipo, 's' para cnpj (ou null)
    $stmt_insert->bind_param("sssss", $nome, $email, $senha_hashed, $tipo, $cnpj);

    if ($stmt_insert->execute()) {
        echo json_encode(["success" => true, "message" => "Cadastro realizado com sucesso! Agora faça o Login!"]);
    } else {
        http_response_code(500); 
        // Em caso de erro de execução (ex: erro de SQL)
        echo json_encode(["success" => false, "message" => "Erro ao cadastrar. Tente novamente. Erro SQL: " . $conn->error]);
    }

    $stmt_insert->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido."]);
}
?>