<!DOCTYPE html>
<?php
include '../verificaLogin.php';
?>
<style>
    #containerTrabalhos{
        width: 90%;
        margin: auto;
    }

    #imagemCarregando{
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
        require 'menuPPGC.php';
        ?>
        <input type="hidden" id="conferencias">
        <input type="hidden" id="periodicos">
        <div id="containerTrabalhos">
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
                        <div class="form-group">
                            <select class="selectpicker" id="categoria">
                                <option value="0">Conferências e Periódicos</option>
                                <option value="1">Conferências</option>
                                <option value="2">Periódicos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="selectpicker" id="professor">
                                <option value="0">Todos professores</option>
                            </select>
                        </div>
                        <input class="btn btn-default" type="button" value="OK" onclick="filtrar()">
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Trabalhos em conferências</div>
                <div class="panel-body">
                    <div id="tabelasIndices">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tabelaConferencias">
                                <thead>
                                    <tr>
                                        <th>Professor</th>
                                        <th>Título do trabalho</th>
                                        <th>Evento</th>
                                        <th>Ano</th>
                                        <th>Estrato</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="panel panel-default">
                <div class="panel-heading">Artigos em periódicos</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tabelaPeriodicos">
                            <thead>
                                <tr>
                                    <th>Professor</th>
                                    <th>Título do trabalho</th>
                                    <th>Periódico</th>
                                    <th>Ano</th>
                                    <th>Estrato</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="imagemCarregando">
            <img src='../../imagens/carregando.gif'>
        </div>
    </div>
</div>
</body>
</html>
<script>
    $(document).ready(function () {
        marcaOpcaoMenu();
        professores();
        trabalhos();
        $('#modalConfirmacao').on('hide.bs.modal', function () {
            exibeTrabalhos(null, null);
        });
    });

    function marcaOpcaoMenu() {
        $('#producoes').addClass('active');
    }

    function professores() {
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'nomeProfessores'},
            success: function (data) {
                for (var i in data) {
                    $("#professor").append('<option value="' + data[i]["DADOS-GERAIS"]["NOME-COMPLETO"] + '">' + data[i]["DADOS-GERAIS"]["NOME-COMPLETO"] + '</option>');
                }
                $("#professor").selectpicker('refresh');
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function trabalhos() {
        $("#containerTrabalhos").hide();
        $("#imagemCarregando").show();
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'trabalhos'},
            success: function (data) {
                $("#containerTrabalhos").show();
                $("#imagemCarregando").hide();
                $("#periodicos").data("per", data.per);
                $("#conferencias").data("conf", data.conf);
                exibeConfirmação(data);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function exibeConfirmação(data) {
        $("#tabelaConfirmacao").html("<tr><th>Evento lattes</th><th>Evento Qualis</th><th></th></tr>");
        var conferencias = data.conf;
        var modal = false;
        for (var i in conferencias) {
            for (var j = 0; j < conferencias[i].length; j++) {
                if (conferencias[i][j].EVENTO_QUALIS !== undefined) {
                    modal = true;
                    $("#tabelaConfirmacao").append("<tr><td>" + conferencias[i][j].EVENTO + "</td><td>"
                            + conferencias[i][j].EVENTO_QUALIS + "</td><td><input name='confirm' type='checkbox' on id='" + i + "_" + j + "' checked></td></tr>");
                }
            }
        }
        if (modal) {
            $("#modalConfirmacao").modal("show");
        } else {
            exibeTrabalhos(null, null);
        }
    }

    function salvaConfirmacoes() {
        $("input:checkbox[name=confirm]:not(:checked)").each(function () {
            var idConf = this.id;
            var profConf = idConf.split("_");
            var conf = $("#conferencias").data("conf");
            conf[profConf[0]][profConf[1]].ESTRATO = "";
            $("#conferencias").data("conf", conf);
        });
        $("#modalConfirmacao").modal("hide");
        exibeTrabalhos(null, null);
        similares();
    }

    function similares() {
        var array = [];
        var conf = $("#conferencias").data("conf");
        $("input:checkbox[name=confirm]:checked").each(function () {
            var idConf = this.id;
            var profConf = idConf.split("_");
            var elemento = {};
            elemento['evento'] = conf[profConf[0]][profConf[1]]["EVENTO"];
            elemento['estrato'] = conf[profConf[0]][profConf[1]]["ESTRATO"];
            elemento['ano'] = conf[profConf[0]][profConf[1]]["ANO"];
            array.push(elemento);
        });
        $("input:checkbox[name=confirm]:not(:checked)").each(function () {
            var idConf = this.id;
            var profConf = idConf.split("_");
            var elemento = {};
            elemento['evento'] = conf[profConf[0]][profConf[1]]["EVENTO"];
            elemento['estrato'] = " ";
            elemento['ano'] = conf[profConf[0]][profConf[1]]["ANO"];
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

    function exibeTrabalhos(conferencias, periodicos) {
        if (conferencias === null) {
            var conferencias = $("#conferencias").data("conf");
        }
        if (periodicos === null) {
            var periodicos = $("#periodicos").data("per");
        }
        $("#tabelaConferencias").html("<thead><tr><th>Professor</th><th>Título do trabalho</th><th>Evento</th><th>Ano</th><th>Estrato</th></tr></thead><tbody>");
        $("#tabelaPeriodicos").html("<thead><tr><th>Professor</th><th>Título do trabalho</th><th>Periódico</th><th>Ano</th><th>Estrato</th></tr></thead><tbody>");

        $("#tabelaConferencias").append("<tbody>");
        $("#tabelaPeriodicos").append("<tbody>");
        for (var i in conferencias) {
            for (var j = 0; j < conferencias[i].length; j++) {
                $("#tabelaConferencias").append("<tr><td>" + i + "</td><td>" + conferencias[i][j].TRABALHO +
                        "</td><td>" + conferencias[i][j].EVENTO + "</td><td>" +
                        conferencias[i][j].ANO + "</td><td>" + conferencias[i][j].ESTRATO + "</td></tr>");
            }
        }
        for (var i in periodicos) {
            for (var j = 0; j < periodicos[i].length; j++) {
                $("#tabelaPeriodicos").append("<tr><td>" + i + "</td><td>" + periodicos[i][j].TITULO +
                        "</td><td>" + periodicos[i][j].PERIODICO + "</td><td>" +
                        periodicos[i][j].ANO + "</td><td>" + periodicos[i][j].ESTRATO + "</td></tr>");
            }
        }
        $("#tabelaConferencias").append("</tbody>");
        $("#tabelaPeriodicos").append("</tbody>");
        $('#tabelaConferencias').DataTable({bDestroy: true, searching: false});
        $('#tabelaPeriodicos').DataTable({bDestroy: true, searching: false});
    }

    function filtrar() {
        $("#containerTrabalhos").hide();
        $("#imagemCarregando").show();
        var ano = $("#ano").val();
        var anoF = $("#anoF").val();
        var estrato = $("#estrato").val();
        var professor = $("#professor").val();
        var categoria = $("#categoria").val();
        var conf = $("#conferencias").data("conf");
        var per = $("#periodicos").data("per");
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'filtrarTrabalhos', 'ano': ano, 'anoF': anoF, 'estrato': estrato, 'professor': professor, 'categoria': categoria,
                'conferencias': conf, 'periodicos': per},
            success: function (data) {
                $("#containerTrabalhos").show();
                $("#imagemCarregando").hide();
                var conferencias = data.conf;
                var periodicos = data.per;
                exibeTrabalhos(conferencias, periodicos);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }
</script>
