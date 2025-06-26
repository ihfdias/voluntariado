<?php
require __DIR__ . '/vendor/autoload.php'; 
require 'conexao.php';                   

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$nome     = $_POST['nome'] ?? '';
$email    = $_POST['email'] ?? '';
$data     = $_POST['data'] ?? '';
$hora     = $_POST['hora'] ?? '';
$mensagem = $_POST['mensagem'] ?? '';


$sql = "SELECT * FROM agendamentos WHERE data = ? AND hora = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $data, $hora);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  echo "⚠️ Já existe um agendamento para esse dia e horário. Por favor, escolha outro.";
  exit;
}


$sql = "INSERT INTO agendamentos (nome, email, data, hora, mensagem) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nome, $email, $data, $hora, $mensagem);

if ($stmt->execute()) {
    
    $mail = new PHPMailer(true);

    try {
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'igorhdias97@gmail.com'; 
        $mail->Password   = 'wemp arcp zpmf aolc';       
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        
        $mail->setFrom('seuemail@gmail.com', 'Agendamento Hospital');
        $mail->addAddress($email, $nome);

        
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


