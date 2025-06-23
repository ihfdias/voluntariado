<?php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "voluntariado"; // o nome que você deu ao seu banco

$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica se deu erro
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
