<!DOCTYPE html>
<?php
include '../verificaLogin.php';
?>
<style>
    #containerTabConf{
        width: 90%;
        margin: auto;
    }

    #filtroContainer{
        width: 200px;
    }

    .form-group{
        margin-right: 10px;
    }

    #imagemLoad{
        text-align: center;
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
    <body>
        <div class="modal fade" id="modalConfirmacao" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Conferências com nomes similares</h4>
                    </div>
                    <div class="modal-body">
                        <p>Por favor, confirme se as seguintes conferências se equivalem</p>
                        <h6>*Por default as conferências são consideradas equivalentes</h6>
                        <div class="table-responsive">
                            <table class="table table-striped" id="tabelaConfirmacao">
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="salvaConfirmacoes()">Salvar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php
        require 'menuDocente.php';
        ?>
        <input type="hidden" id="conferencias">
        <div id="containerTabConf">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="ano">Ano: De</label>
                            <input type="text" class="form-control" id="ano" placeholder="YYYY">
                        </div>
                        <div class="form-group">
                            <label for="ano">Até</label>
                            <input type="text" class="form-control" id="anoF" placeholder="YYYY">
                        </div>
                        <div class="form-group">
                            <label for="estrato">Estrato</label>
                            <select class="selectpicker" id="estrato">
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
                <div class="table-responsive">
                    <table class="table table-striped" id="tabelaConferencias"></table>
                </div>
            </div>
        </div>
        <div class="tab-content" id="imagemLoad"><img src='../../imagens/carregando.gif'></div>
    </body>
</html>
<script>
    $(document).ready(function () {
        trabalhos();
        marcaOpcaoMenu();
        $('#modalConfirmacao').on('hide.bs.modal', function () {
            exibeConferencias(null);
        });
    });

    function marcaOpcaoMenu() {
        $('#producoes').addClass('active');
    }

    function trabalhos() {
        var nome = "<?= $_SESSION['nomeUsuario'] ?>";
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'confDocente', 'nome': nome},
            success: function (conf) {
                $("#imagemLoad").hide();
                $("#conferencias").data("conf", conf);
                exibeConfirmacao(conf);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function filtrar() {
        var nome = "<?= $_SESSION['nomeUsuario'] ?>";
        var ano = $("#ano").val();
        var anoF = $("#anoF").val();
        var estrato = $("#estrato").val();
        var conf = $("#conferencias").data("conf");
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'filtraConf', 'nome': nome, 'ano': ano, 'anoF': anoF, 'estrato': estrato, 'conferencias': conf},
            success: function (conf) {
                exibeConferencias(conf);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function exibeConferencias(conf) {
        $("#tabelaConferencias").children().remove();
        if (conf === null) {
            var conf = $("#conferencias").data("conf");
        }
        $("#tabelaConferencias").html("<thead><tr><th>Título do trabalho</th><th>Evento</th><th>Ano</th><th>Estrato</th></tr></thead><tbody>");
        for (var i = 0; i < conf.length; i++) {
            $("#tabelaConferencias").append("<tr><td>" + conf[i].TRABALHO + "</td><td>"
                    + conf[i].EVENTO + "</td><td>" + conf[i].ANO + "</td><td id='conf" + i + "'>"
                    + conf[i].ESTRATO + "</td></tr>");
        }
        $("#tabelaConferencias").append("</tbody>");
        $('#tabelaConferencias').DataTable({bDestroy: true, searching: false});
    }

    function exibeConfirmacao(conf) {
        $("#tabelaConfirmacao").html("<tr><th>Evento lattes</th><th>Evento Qualis</th><th></th></tr>");
        var modal = false;
        for (var i = 0; i < conf.length; i++) {
            if (conf[i].EVENTO_QUALIS !== undefined) {
                modal = true;
                $("#tabelaConfirmacao").append("<tr><td>" + conf[i].EVENTO + "</td><td>"
                        + conf[i].EVENTO_QUALIS + "</td><td><input name='confirm' type='checkbox' on id='" + i + "' checked></td></tr>");
            }
        }
        if (modal) {
            $("#modalConfirmacao").modal("show");
        } else {
            exibeConferencias(null);
        }
    }

    function salvaConfirmacoes() {
        similares();
        $("input:checkbox[name=confirm]:not(:checked)").each(function () {
            var conf = $("#conferencias").data("conf");
            conf[this.id].ESTRATO = "";
            $("#conferencias").data("conf", conf);
        });
        $("#modalConfirmacao").modal("hide");
        exibeConferencias(null);
    }

    function similares() {
        var array = [];
        var conf = $("#conferencias").data("conf");
        $("input:checkbox[name=confirm]:checked").each(function () {
            var elemento = {};
            elemento['evento'] = conf[this.id]["EVENTO"];
            elemento['estrato'] = conf[this.id]["ESTRATO"];
            elemento['ano'] = conf[this.id]["ANO"];
            array.push(elemento);
        });
        $("input:checkbox[name=confirm]:not(:checked)").each(function () {
            var elemento = {};
            elemento['evento'] = conf[this.id]["EVENTO"];
            elemento['estrato'] = " ";
            elemento['ano'] = conf[this.id]["ANO"];
            array.push(elemento);
        });
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'inserirSimilares', 'conferencias': array},
            success: function () {
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }
</script>
