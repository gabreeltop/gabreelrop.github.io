<?php
// Inicia a sessão
session_start();

// Destrói a sessão e remove todos os dados associados
session_unset();
session_destroy();

// Redireciona para a página de login
header("Location: index.php");
exit();
?>
