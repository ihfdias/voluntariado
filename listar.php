<?php
require_once 'conexao.php';

$agendamentos = $db->query("SELECT * FROM agendamentos ORDER BY data, hora");

echo "<h1>Agendamentos Marcados</h1><ul>";
foreach ($agendamentos as $a) {
    echo "<li>{$a['data']} às {$a['hora']} - {$a['nome']}</li>";
}
echo "</ul><br><a href='index.html'>Voltar</a>";
?>
