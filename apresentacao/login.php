<?php

require_once '../dados/DAO/DAO.php';

session_start();

$login = strtoupper($_POST['login']);
$senha = $_POST['senha'];
$dao = new DAO();
$filter = ["siape" => "$senha", "login" => "$login"];
$options = ['projection' => ['_id' => 0]];
$result = $dao->select("siape", $filter, $options);
$rows = json_decode($result, TRUE);
if (!empty($rows)) {
    $_SESSION['nomeUsuario'] = mb_convert_case($rows[0]['nome'], MB_CASE_TITLE);
    header('location:docente/indexDocente.php');
} else {
    $filter = ["senha" => "$senha", "login" => "$login"];
    $options = ['projection' => ['_id' => 0]];
    $result = $dao->select("ppgc", $filter, $options);
    $rows = json_decode($result, TRUE);
    if (!empty($rows)) {
        $_SESSION['nomeUsuario'] = "Coordenador";
        header('location:PPGC/trabalhosPPGC.php');
    } else {
        unset($_SESSION['nomeUsuario']);
        header('location:index.php');
    }
}

