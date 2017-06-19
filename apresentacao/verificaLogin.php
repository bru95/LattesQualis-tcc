<?php

session_start();
if ((!isset($_SESSION['nomeUsuario']) == true)) {
    unset($_SESSION['nomeUsuario']);
    header('location:../index.php');
}