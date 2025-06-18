<?php
require_once 'conexao.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$data = $_POST['data'];
$hora = $_POST['hora'];
$mensagem = $_POST['mensagem'];

// Verifica se já existe agendamento no mesmo dia e hora
$stmt = $db->prepare("SELECT * FROM agendamentos WHERE data = ? AND hora = ?");
$stmt->execute([$data, $hora]);

if ($stmt->rowCount() > 0) {
    echo "Já existe um grupo agendado nesse dia e horário.";
    echo '<br><a href="index.html">Voltar</a>';
} else {
    $insert = $db->prepare("INSERT INTO agendamentos (nome, email, data, hora, mensagem) VALUES (?, ?, ?, ?, ?)");
    $insert->execute([$nome, $email, $data, $hora, $mensagem]);
    echo "Agendamento realizado com sucesso!";
    echo '<br><a href="index.html">Novo agendamento</a>';
}
?>
