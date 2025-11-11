<?php
include 'conecta.php';

$id = $_POST['id'] ?? '';
$codigo = $_POST['codigo'];
$nome = $_POST['nome'];
$categoria = $_POST['categoria'];
$preco = $_POST['preco'];
$estoque = $_POST['estoque'];
$imagem = $_POST['imagem'];

if ($id) {
  $sql = "UPDATE produtos SET 
            codigo='$codigo', 
            nome='$nome', 
            categoria='$categoria', 
            preco='$preco', 
            estoque='$estoque', 
            imagem='$imagem'
          WHERE id='$id'";
  $msg = "Produto atualizado com sucesso!";
} else {
  $sql = "INSERT INTO produtos (codigo, nome, categoria, preco, estoque, imagem)
          VALUES ('$codigo', '$nome', '$categoria', '$preco', '$estoque', '$imagem')";
  $msg = "Produto cadastrado com sucesso!";
}

if ($conn->query($sql)) {
  echo json_encode(['success' => true, 'message' => $msg]);
} else {
  echo json_encode(['success' => false, 'message' => 'Erro ao salvar produto.']);
}
?>
