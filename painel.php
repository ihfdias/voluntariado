<?php
// painel.php
session_start();
if (!isset($_SESSION['adm'])) {
    header("Location: login.php");
    exit;
}

require 'conexao.php';

// Excluir agendamento
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM agendamentos WHERE id = $id");
    header("Location: painel.php");
    exit;
}

// Marcar como concluído
if (isset($_GET['concluir'])) {
    $id = intval($_GET['concluir']);
    $conn->query("UPDATE agendamentos SET concluido = 1 WHERE id = $id");
    header("Location: painel.php");
    exit;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$filtroData = isset($_GET['data']) ? $_GET['data'] : '';

$sql = "SELECT * FROM agendamentos WHERE 1=1";

if (!empty($busca)) {
    $sql .= " AND nome LIKE '%$busca%'";
}

if (!empty($filtroData)) {
    $sql .= " AND data = '$filtroData'";
}

$sql .= " ORDER BY data, hora";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            flex-direction: column;
            background-color: #121212;
        }

        .painel {
            background-color: #222;
            padding: 30px;
            border-radius: 10px;
            max-width: 1000px;
            width: 100%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #333;
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #444;
            text-align: left;
            color: #fff;
        }

        th {
            background-color: #444;
        }

        a.btn {
            padding: 6px 12px;
            background-color: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            margin-right: 5px;
            font-size: 14px;
        }

        a.btn.delete {
            background-color: #dc3545;
        }

        a.btn.done {
            background-color: #17a2b8;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .top-bar a {
            color: #00aaff;
            text-decoration: none;
        }

        .form-busca {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .form-busca input[type="text"],
        .form-busca input[type="date"] {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            flex: 1;
            min-width: 150px;
        }

        .form-busca button {
            background-color: #00aaff;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .form-busca button:hover {
            background-color: #008ecc;
        }

        .form-busca .btn-link {
            color: #00aaff;
            text-decoration: none;
            align-self: center;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="painel">
        <div class="top-bar">
            <h2>Bem-vindo, <?php echo $_SESSION['adm']; ?>!</h2>
            <a href="logout.php">Sair</a>
        </div>

        <form method="GET" class="form-busca">
            <input type="text" name="busca" placeholder="Buscar por nome" value="<?php echo htmlspecialchars($busca); ?>">
            <input type="date" name="data" value="<?php echo $filtroData; ?>">
            <button type="submit">Filtrar</button>
            <a href="painel.php" class="btn-link">Limpar filtros</a>
        </form>

        <h3>Agendamentos</h3>
        <table>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Mensagem</th>
                <th>Ações</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['nome'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['data'] ?></td>
                    <td><?= $row['hora'] ?></td>
                    <td><?= $row['mensagem'] ?></td>
                    <td>
                        <a class="btn" href="editar.php?id=<?= $row['id'] ?>">Editar</a>
                        <a class="btn delete" href="painel.php?excluir=<?= $row['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                        <?php if (!isset($row['concluido']) || !$row['concluido']): ?>
                            <a class="btn done" href="painel.php?concluir=<?= $row['id'] ?>">Concluir</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>