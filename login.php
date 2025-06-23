<?php
session_start();
require 'conexao.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $usuario = $res->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['adm'] = $usuario['nome'];
            header("Location: painel.php");
            exit;
        } else {
            echo "❌ Senha incorreta.";
        }
    } else {
        echo "❌ E-mail não encontrado.";
    }
}
?>

<!-- Formulário -->
<form method="POST">
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="password" name="senha" placeholder="Senha" required>
    <button type="submit">Entrar</button>
</form>
