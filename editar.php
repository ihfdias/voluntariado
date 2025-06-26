<?php
session_start();
if (!isset($_SESSION['adm'])) {
    header("Location: login.php");
    exit;
}

// Carrega o autoload do Composer e o .env
require __DIR__ . '/vendor/autoload.php';
require 'conexao.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verifica se veio o ID
if (!isset($_GET['id'])) {
    echo "ID do agendamento não informado.";
    exit;
}

$id = intval($_GET['id']);

// Busca os dados do agendamento
$sql = "SELECT * FROM agendamentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Agendamento não encontrado.";
    exit;
}

$agendamento = $result->fetch_assoc();

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $mensagem = $_POST['mensagem'];

    // Verifica duplicidade de data e hora
    $check = $conn->prepare("SELECT id FROM agendamentos WHERE data = ? AND hora = ? AND id != ?");
    $check->bind_param("ssi", $data, $hora, $id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        $erro = "Já existe um agendamento para esse dia e horário.";
    } else {
        $update = $conn->prepare("UPDATE agendamentos SET nome = ?, email = ?, data = ?, hora = ?, mensagem = ? WHERE id = ?");
        $update->bind_param("sssssi", $nome, $email, $data, $hora, $mensagem, $id);

        if ($update->execute()) {
            // Enviar e-mail de confirmação
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'igorhdias97@gmail.com'; // Altere aqui
                $mail->Password = '$2y$10$wcfUtI4pW5uCzqtJFp005uUo0kLDFF3SV9bBJvz7sykVxuKClsZCO'; // Altere aqui
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('igorhdias97@gmail.com', 'Voluntariado Hospital');
                $mail->addAddress($email, $nome);

                $mail->isHTML(true);
                $mail->Subject = 'Agendamento Atualizado com Sucesso';
                $mail->Body    = "<p>Olá <strong>$nome</strong>, seu agendamento foi atualizado com sucesso.</p>"
                             . "<p><strong>Data:</strong> $data<br>"
                             . "<strong>Hora:</strong> $hora</p>"
                             . "<p>Qualquer dúvida, entre em contato conosco.</p>";

                $mail->send();
            } catch (Exception $e) {
                // Logar erro ou ignorar
            }

            header("Location: painel.php");
            exit;
        } else {
            $erro = "Erro ao atualizar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            flex-direction: column;
            background-color: #121212;
        }

        .formulario {
            background-color: #222;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            margin: 30px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .erro {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
        }

        a.voltar {
            display: block;
            margin-top: 15px;
            color: #00aaff;
            text-align: center;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="formulario">
        <h2>Editar Agendamento</h2>
        <?php if ($erro): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-box">
                <input type="text" name="nome" value="<?php echo htmlspecialchars($agendamento['nome']); ?>" required>
            </div>

            <div class="input-box">
                <input type="email" name="email" value="<?php echo htmlspecialchars($agendamento['email']); ?>" required>
            </div>

            <div class="input-box">
                <input type="date" name="data" value="<?php echo $agendamento['data']; ?>" required>
            </div>

            <div class="input-box">
                <input type="text" id="hora" name="hora" value="<?php echo $agendamento['hora']; ?>" required>
            </div>

            <div class="input-box">
                <textarea name="mensagem" rows="3"><?php echo htmlspecialchars($agendamento['mensagem']); ?></textarea>
            </div>

            <button type="submit" class="login">Salvar</button>
        </form>

        <a href="painel.php" class="voltar">&larr; Voltar para o painel</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#hora", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 30
        });
    </script>
</body>
</html>
