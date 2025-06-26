<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host   = $_ENV['DB_HOST'];
$usuario = $_ENV['DB_USER'];
$senha  = $_ENV['DB_PASS'];
$banco  = $_ENV['DB_NAME'];

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
