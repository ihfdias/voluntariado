<?php
require_once 'conexao.php';

$nome = $_POST['nome'];
$data = $_POST['data'];
$hora = $_POST['hora'];

// Verificar se já existe um agendamento nesse dia e hora
$stmt = $db->prepare("SELECT * FROM agendamentos WHERE data = ? AND hora = ?");
$stmt->execute([$data, $hora]);

if ($stmt->rowCount() > 0) {
    echo "Já existe um grupo agendado nesse dia e horário.";
    echo '<br><a href="index.html">Voltar</a>';
} else {
    $insert = $db->prepare("INSERT INTO agendamentos (nome, data, hora) VALUES (?, ?, ?)");
    $insert->execute([$nome, $data, $hora]);
    echo "Agendamento realizado com sucesso!";
    echo '<br><a href="index.html">Novo agendamento</a>';
}
?>
