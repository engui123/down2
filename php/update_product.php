<?php
include 'conecta.php';
header('Content-Type: application/json');

// Coletando os dados
$id = $_POST['id'] ?? 0;
$codigo = $_POST['codigo'] ?? ''; // <-- Adicionado corretamente
$nome = $_POST['nome'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$preco = $_POST['preco'] ?? 0;
$estoque = $_POST['quantidade'] ?? 0;
$unidade = $_POST['unidade'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$imagem = $_POST['imagem'] ?? '';

// Verifica se o código foi preenchido
if (empty($codigo)) {
    echo json_encode(['success' => false, 'error' => 'Código de barras é obrigatório']);
    exit;
}

// Verifica duplicidade de código de barras
$stmt = $conn->prepare("SELECT id FROM produtos WHERE codigo = ? AND id <> ?");
$stmt->bind_param("si", $codigo, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Já existe um produto com este código de barras.']);
    exit;
}
$stmt->close();

// Se ID for informado, atualiza o produto
if ($id) {
    $sql = "UPDATE produtos SET nome=?, codigo=?, descricao=?, categoria=?, preco=?, estoque=?, unidade=?, imagem=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdssi", $nome, $codigo, $descricao, $categoria, $preco, $estoque, $unidade, $imagem, $id);
} else {
    // Caso contrário, insere novo produto
    $sql = "INSERT INTO produtos (nome, codigo, descricao, categoria, preco, estoque, unidade, imagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdiss", $nome, $codigo, $descricao, $categoria, $preco, $estoque, $unidade, $imagem);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
