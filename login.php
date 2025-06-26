<?php
session_start();

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Pegando os dados do .env
    $usuarioCorreto = $_ENV['LOGIN_USUARIO'];
    $senhaCorreta = $_ENV['LOGIN_SENHA'];

    if ($usuario === $usuarioCorreto && $senha === $senhaCorreta) {
        $_SESSION['adm'] = $usuario;
        header("Location: painel.php");
        exit;
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login Administrativo</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="formulario">
    <h2>Login</h2>

    <?php if (!empty($erro)): ?>
      <p style="color: #dc3545; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="input-box">
        <input type="text" name="usuario" placeholder="Usuário" required>
        <i class="fas fa-user"></i>
      </div>

      <div class="input-box">
        <input type="password" name="senha" placeholder="Senha" required>
        <i class="fas fa-lock"></i>
      </div>

      <button type="submit" class="login">Entrar</button>
    </form>

    <div class="register-link">
      <a href="index.html">Voltar</a>
    </div>
  </div>
</body>
</html>
