<?php
require_once 'conexao.php';

$agendamentos = $db->query("SELECT * FROM agendamentos ORDER BY data, hora");

echo "<h1>Agendamentos</h1><ul>";
foreach ($agendamentos as $a) {
    echo "<li>";
    echo "<strong>{$a['data']} às {$a['hora']}</strong><br>";
    echo "Nome: {$a['nome']}<br>";
    echo "Email: {$a['email']}<br>";
    echo "Mensagem: {$a['mensagem']}<br><br>";
    echo "</li>";
}
echo "</ul><br><a href='index.html'>Voltar</a>";
?>
