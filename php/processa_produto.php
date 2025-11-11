<?php
include 'conecta.php';
header('Content-Type: application/json');

$nome = trim($_POST['nome'] ?? '');
$codigo = trim($_POST['codigo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$preco = isset($_POST['preco']) ? str_replace(',', '.', $_POST['preco']) : null;
$preco = (float)$preco;
$estoque = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : null;
$unidade = trim($_POST['unidade'] ?? '');
$imagem = trim($_POST['imagem'] ?? '');
$id = isset($_POST['id']) ? (int)$_POST['id'] : null;

if (!$nome || !$codigo || !$categoria || $preco === null || $estoque === null || !$unidade) {
    echo json_encode(['success'=>false,'error'=>'Campos obrigatórios não preenchidos']);
    exit;
}

// Verifica duplicidade
if ($id) {
    $stmt = $conn->prepare("SELECT id FROM produtos WHERE codigo=? AND id<>?");
    $stmt->bind_param("si",$codigo,$id);
} else {
    $stmt = $conn->prepare("SELECT id FROM produtos WHERE codigo=?");
    $stmt->bind_param("s",$codigo);
}
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows>0) {
    echo json_encode(['success'=>false,'error'=>'Já existe um produto com este código']);
    exit;
}

// Inserir ou atualizar
if($id){
    $stmt = $conn->prepare("UPDATE produtos SET nome=?, codigo=?, descricao=?, categoria=?, preco=?, estoque=?, unidade=?, imagem=? WHERE id=?");
    $stmt->bind_param("ssssdisi",$nome,$codigo,$descricao,$categoria,$preco,$estoque,$unidade,$imagem,$id);
}else{
    $stmt = $conn->prepare("INSERT INTO produtos (nome,codigo,descricao,categoria,preco,estoque,unidade,imagem) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssdiis",$nome,$codigo,$descricao,$categoria,$preco,$estoque,$unidade,$imagem);
}

if($stmt->execute()){
    echo json_encode(['success'=>true]);
}else{
    echo json_encode(['success'=>false,'error'=>$conn->error]);
}
?>
