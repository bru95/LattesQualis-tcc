<!DOCTYPE html>
<?php
include '../verificaLogin.php';
?>
<style>

    #professoresNav{
        width: 20%;
        float:left;
        overflow-y: scroll;
        position:relative;
        height: 550px;
    }

    #containerIndices{
        width: 80%;
        float:left;
    }

    #imagemLoad{
        text-align: center;
    }
    
    .tableProfsIndices{
        text-align: center;
        vertical-align:middle;
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
        <div id="conferencias"></div>
        <div id="periodicos"></div>
        <ul class="nav nav-pills  nav-stacked" id="professoresNav"></ul>
        <div id="containerIndices">
            <div class="container">
                <h4>Selecionar período para índices</h4>
                <form class="form-inline">
                    <div class="form-group">
                        <label for="anoIndice">Ano de solicitação</label>
                        <input type="text" class="form-control" id="anoSolicitação" >
                    </div>
                    <input type="hidden" value="<?= date("Y") ?>" id="anoIndice">
                    <input type="button" class="btn btn-default" value="OK" onclick="calculaIndice();">
                </form>
                <h6><p>*O perído considerado para o cálculo do índice é o ano de solicitação e os três anteriores</p></h6>
            </div>
            <div class="tab-content" id="indices"></div>
            <div class="tab-content" id="imagemLoad"><img src='../../imagens/carregando.gif'></div>
        </div>
    </body>
</html>
<script>
    $(document).ready(function () {
        marcaOpcaoMenu();
        trabalhosProfessores();
        $('#modalConfirmacao').on('hide.bs.modal', function () {
            indicesProfessores();
        });
    });

    function marcaOpcaoMenu() {
        $('#ppgi').addClass('active');
    }

    function trabalhosProfessores() {
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'trabalhos'},
            success: function (data) {
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
            $("#imagemLoad").hide();
            $("#modalConfirmacao").modal("show");
        } else {
            indicesProfessores();
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
        indicesProfessores();
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

    function indicesProfessores() {
        var ano = $("#anoIndice").val();
        var per = $("#periodicos").data("per");
        var conf = $("#conferencias").data("conf");
        $("#indices").html("");
        $("#professoresNav").html("");
        $("#imagemLoad").show();
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'indicesProfessores', 'ano': ano, 'conferencias': conf, 'periodicos': per},
            success: function (obj) {
                var j = 0;
                $("#imagemLoad").hide();
                for (var i in obj) {
                    $("#professoresNav").append('<li role="presentation" ><a href="#' + j + '" data-toggle="pill">' +
                            i + '</a></li>');
                    var conteudo = '<div role="tabpanel" class="tab-pane fade" id="' + j + '">' +
                            '<div class="panel-body" ><div class="table-responsive" ><table class="table table-striped table-bordered tableProfsIndices">' +
                            '<tr><th class="text-center">Estrato</th><th class="text-center">Quantidade</th></tr><tr><td>A1</td><td>' + obj[i]["qtd"]["A1"] +
                            '</td></tr><tr><td>A2</td><td>' + obj[i]["qtd"]["A2"] + '</td></tr><tr><td>B1</td><td>' + obj[i]["qtd"]["B1"] +
                            '</td></tr></tr><td>B2</td><td>' + obj[i]["qtd"]["B2"] + '</td></tr><tr><td>B3</td><td>' + obj[i]["qtd"]["B3"] +
                            '</td></tr><tr><td>B4</td><td>' + obj[i]["qtd"]["B4"] + '</td></tr><tr><td>B5</td><td>' + obj[i]["qtd"]["B5"] +
                            '</td></tr></table><h6 id="periodo">*Período de ' + (ano - 3) + ' a ' + ano +
                            '</h6></div><h5>Orientações concluídas:' + obj[i]["orientacoes"] + '</h5><br>';
                    conteudo += '<div class="alert alert-info" role="alert">Índice geral: ' + obj[i]["ig"] + '</div>';
                    conteudo += '<div class="alert alert-info" role="alert" id="ir">Índice de Recadastramento: ' + obj[i]["ir"] + '</div></div>' + '</div>';
                    $("#indices").append(conteudo);
                    j++;
                }
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function calculaIndice() {
        var anoSolicitacao = $("#anoSolicitação").val();
        $("#anoIndice").val(anoSolicitacao);
        indicesProfessores();
    }
</script>

