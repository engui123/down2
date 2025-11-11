<?php
include 'conecta.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID não fornecido']);
    exit;
}

$id = $_GET['id'];

// Busca o produto pelo ID
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $produto = $result->fetch_assoc();
    echo json_encode($produto); // retorna JSON do produto
} else {
    echo json_encode(['error' => 'Produto não encontrado']);
}
?>
