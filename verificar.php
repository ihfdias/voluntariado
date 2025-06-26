<?php
require 'conexao.php';

$agendamentos = [];
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $nome = trim($_POST['nome']);

    if (empty($email) && empty($nome)) {
        $erro = "Informe o nome ou e-mail.";
    } else {
        $sql = "SELECT * FROM agendamentos WHERE 1";
        $params = [];
        $types = "";

        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = "E-mail inválido.";
            } else {
                $sql .= " AND email = ?";
                $params[] = $email;
                $types .= "s";
            }
        }

        if (!empty($nome)) {
            $sql .= " AND nome LIKE ?";
            $params[] = "%$nome%";
            $types .= "s";
        }

        if (!$erro) {
            $sql .= " ORDER BY data, hora";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $agendamentos = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $erro = "Nenhum agendamento encontrado com esses dados.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Verificar Agendamento</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            flex-direction: column;
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 20 20px;
            margin-top: 30px;
        }

        th,
        td {
            padding: 12px 16px;
            background-color: #2a2a2a;
            border: none;
            border-radius: 8px;
            text-align: center;
            color: #fff;
        }

        th {
            background-color: #444;
            font-weight: bold;
        }

        tbody tr:hover td {
            background-color: #333;
        }

        .erro {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
        }

        .voltar {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #00aaff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="formulario">
        <h2>Verificar Agendamento</h2>

        <?php if ($erro): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-box">
                <input type="text" name="nome" placeholder="Digite seu nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                <i class="fas fa-user"></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Digite seu e-mail" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <i class="fas fa-envelope"></i>
            </div>
            <button type="submit" class="login">Verificar</button>
        </form>

        <a href="index.html" class="voltar">&larr; Voltar ao início</a>

        <?php if ($agendamentos): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Mensagem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $ag): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ag['nome']); ?></td>
                            <td><?php echo $ag['data']; ?></td>
                            <td><?php echo $ag['hora']; ?></td>
                            <td><?php echo htmlspecialchars($ag['mensagem']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>