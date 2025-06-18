<?php
$host = 'localhost';
$dbname = 'voluntariado';
$user = 'root';       // padrão do XAMPP/Laragon
$pass = '';           // senha vazia (a menos que você tenha definido)

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
