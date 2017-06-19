<!DOCTYPE html>
<?php
include '../verificaLogin.php';
?>
<style>
    #containerTabPer{
        width: 90%;
        margin: auto;
    }

    #filtroContainer{
        width: 200px;
    }

    .form-group{
        margin-right: 10px;
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

        <link rel="stylesheet" href="../../bootstrap-select/bootstrap-select.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="../../bootstrap-select/bootstrap-select.min.js"></script>
        
        <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
    </head>
    <body onload="marcaOpcaoMenu(); trabalhos()">
        <?php
        require 'menuDocente.php';
        ?>
        <div id="containerTabPer">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="ano">Ano: De</label>
                            <input type="text" class="form-control" id="anoPer" placeholder="YYYY">
                        </div>
                        <div class="form-group">
                            <label for="ano">Até</label>
                            <input type="text" class="form-control" id="anoF" placeholder="YYYY">
                        </div>
                        <div class="form-group">
                            <label for="estrato">Estrato</label>
                            <select class="selectpicker" id="estratoPer">
                                <option value="">Todos</option>
                                <option value="A1">A1</option>
                                <option value="A2">A2</option>
                                <option value="B1">B1</option>
                                <option value="B2">B2</option>
                                <option value="B3">B3</option>
                                <option value="B4">B4</option>
                                <option value="B5">B5</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                        <input class="btn btn-default" type="button" value="OK" onclick="filtrar()">
                    </form>
                </div>
                <table class="table table-striped" id="tabelaPeriodicos">
                    <tr>
                        <th>Título do trabalho</th>
                        <th>Periódico</th>
                        <th>Ano</th>
                        <th>Estrato</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<script>
    function marcaOpcaoMenu() {
        $('#producoes').addClass('active');
    }

    function trabalhos() {
        var nome = "<?= $_SESSION['nomeUsuario'] ?>";
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'perDocente', 'nome': nome},
            success: function (per) {
                exibePeriodicos(per);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function filtrar() {
        var nome = "<?= $_SESSION['nomeUsuario'] ?>";
        var ano = $("#anoPer").val();
        var anoF = $("#anoF").val();
        var estrato = $("#estratoPer").val();
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'filtraPer', 'nome': nome, 'ano': ano, 'anoF': anoF, 'estrato': estrato},
            success: function (per) {
                exibePeriodicos(per);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function exibePeriodicos(per) {
        $("#tabelaPeriodicos").html("<thead><tr><th>Título do trabalho</th><th>Periódico</th><th>Ano</th><th>Estrato</th></tr></thead><tbody>");
        for (var i = 0; i < per.length; i++) {
            $("#tabelaPeriodicos").append("<tr><td>" + per[i].TITULO + "</td><td>"
                    + per[i].PERIODICO + "</td><td>" + per[i].ANO + "</td><td>"
                    + per[i].ESTRATO + "</td></tr>");
        }
        $("#tabelaPeriodicos").append("</tbody>");
        $('#tabelaPeriodicos').DataTable({bDestroy:true, searching: false});
    }
</script>
