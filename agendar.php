<?php
require __DIR__ . '/vendor/autoload.php'; // Carrega todas as bibliotecas
require 'conexao.php';                   // Sua conexão com o banco

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Pegando dados do formulário
$nome     = $_POST['nome'] ?? '';
$email    = $_POST['email'] ?? '';
$data     = $_POST['data'] ?? '';
$hora     = $_POST['hora'] ?? '';
$mensagem = $_POST['mensagem'] ?? '';

// Verificar se já existe agendamento nesse horário
$sql = "SELECT * FROM agendamentos WHERE data = ? AND hora = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $data, $hora);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  echo "⚠️ Já existe um agendamento para esse dia e horário. Por favor, escolha outro.";
  exit;
}

// Inserir no banco
$sql = "INSERT INTO agendamentos (nome, email, data, hora, mensagem) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nome, $email, $data, $hora, $mensagem);

if ($stmt->execute()) {
    // Envia e-mail de confirmação
    $mail = new PHPMailer(true);

    try {
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'igorhdias97@gmail.com'; // seu e-mail Gmail
        $mail->Password   = 'wemp arcp zpmf aolc';       // senha gerada pelo Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('seuemail@gmail.com', 'Agendamento Hospital');
        $mail->addAddress($email, $nome);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de Agendamento';
        $mail->Body    = "
          <h2>Olá, $nome</h2>
          <p>Seu agendamento foi confirmado com sucesso para:</p>
          <p><strong>Data:</strong> $data<br>
             <strong>Horário:</strong> $hora</p>
          <p>Mensagem enviada: <i>$mensagem</i></p>
          <br>
          <p>Se precisar cancelar, entre em contato conosco.</p>";

        $mail->send();
        echo "✅ Agendamento realizado e e-mail enviado com sucesso.";
    } catch (Exception $e) {
        echo "Agendamento salvo, mas erro ao enviar o e-mail: {$mail->ErrorInfo}";
    }
} else {
    echo "❌ Erro ao salvar o agendamento.";
}
?>


