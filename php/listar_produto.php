<?php
include 'conecta.php';

$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);

$produtos = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
  }
}

header('Content-Type: application/json');
echo json_encode($produtos);
?>
