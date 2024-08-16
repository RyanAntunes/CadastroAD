<?php

require_once('authenticate.php');

// Verificar se o usuário está logado
if (strpos($_SERVER['SCRIPT_NAME'], 'login.php') === false) { 
    if (empty($_SESSION['user'])) { 
        header('Location: login.php'); 
        die("You are not logged in."); 
    } 
}

$base = $_SERVER['DOCUMENT_ROOT'];
$home = $base;  // Não há necessidade de adicionar uma string vazia

$url = '';

$date = date('Y-m-d H:i:s');

// Configuração correta de relatórios de erro
ini_set("error_reporting", E_ALL); // Define o nível de erro como E_ALL
error_reporting(E_ALL); // Configura o relatório de erros para E_ALL

?>
