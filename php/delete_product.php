<?php
include 'conecta.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID do produto não fornecido']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);

if($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Produto excluído com sucesso']);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
?>
