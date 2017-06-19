<!DOCTYPE html>
<?php
include '../verificaLogin.php';
?>
<style>
    #dadosBasicos{
        width: 90%;
        margin: auto;
    }
</style>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>TCC Bruna</title>

        <!-- Bootstrap -->
        <link href="../../bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="../../jquery/jquey.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="../../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    </head>
    <body onload="marcaOpcaoMenu();dadosBasicos();">
        <?php
        require 'menuDocente.php';
        ?>
        <div class="panel panel-default" id="dadosBasicos">
            <div class="panel-body">
                <div class="page-header">
                    <h1><small>Bem-vindo(a) <?= $_SESSION['nomeUsuario'] ?></small></h1>
                </div>
                <div class="well" id="tableDados"></div>
            </div>
        </div>
    </body>
</html>
<script>
    function marcaOpcaoMenu() {
        $('#home').addClass('active');
    }

    function dadosBasicos() {
        var nome = "<?= $_SESSION['nomeUsuario'] ?>";
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'dadosProf', 'nome': nome},
            success: function (data) {
                $("#tableDados").append("Nome em citações bibliográficas: " + data[0]["DADOS-GERAIS"]["NOME-EM-CITACOES-BIBLIOGRAFICAS"]);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }
</script>
