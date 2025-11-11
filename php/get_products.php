<?php
include 'conecta.php';

$sql = "SELECT id, codigo, nome, categoria, preco, estoque, imagem 
        FROM produtos ORDER BY criado_em DESC";
$result = $conn->query($sql);

$produtos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($produtos);
