<!DOCTYPE html>
<style>
    #panelIndices{
        width: 40%;
        margin-left: 100px;
        float:left;
    } 

    #panelPeriodo{
        width: 40%;
        margin-left: 100px;
        float:left;
    }

    #tabelaEstratos{
        width: 90%;
        text-align: center;
    }
</style>
<?php
include '../verificaLogin.php';
?>
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
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="similares()">Salvar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php
        require 'menuDocente.php';
        ?>
        <input type="hidden" id="conferencias">
        <div class="panel panel-default" id="panelIndices">
            <div class="panel-body" >
                <div class="table-responsive" >
                    <table class="table table-striped table-bordered" id="tabelaEstratos">
                    </table>
                    <h6 id="periodo">*Período de 2014 a 2017</h6>
                </div>
                <h5 id="orientacoes"></h5><br>
                <div class="alert alert-info" role="alert" id="ig"></div>
                <div class="alert alert-info" role="alert" id="ir"></div>
            </div>
        </div>
        <div class="panel panel-default" id="panelPeriodo">
            <div class="panel-body" >
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
        </div>
        <div class="tab-content" id="imagemLoad"><img src='../../imagens/carregando.gif'></div>
    </body>
</html>
<script>
    $(document).ready(function () {
        marcaOpcaoMenu();
        trabalhos();
        $('#modalConfirmacao').on('hide.bs.modal', function () {
            salvaConfirmacoes();
            indices();
        });
    });

    function marcaOpcaoMenu() {
        $('#ppgc').addClass('active');
    }

    function trabalhos() {
        var nome = "<?= $_SESSION['nomeUsuario'] ?>";
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'confDocente', 'nome': nome},
            success: function (conf) {
                $("#conferencias").data("conf", conf);
                exibeConfirmacao(conf);
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
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
            indices();
        }
    }

    function salvaConfirmacoes() {
        $("input:checkbox[name=confirm]:not(:checked)").each(function () {
            var conf = $("#conferencias").data("conf");
            conf[this.id].ESTRATO = "";
            $("#conferencias").data("conf", conf);
        });
    }

    function indices() {
        var nome = "<?= $_SESSION['nomeUsuario'] ?>";
        var ano = $("#anoIndice").val();
        var conf = $("#conferencias").data("conf");
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            data: {'acao': 'indicesDocente', 'nome': nome, 'ano': ano, 'conferencias': conf},
            success: function (data) {
                $("#imagemLoad").hide();
                var obj = JSON.parse(data);
                var estratos = obj.qtd;
                $("#periodo").html("*Período de " + (Number(ano) - 3) + " a " + ano);
                $("#tabelaEstratos").html("<tr><th style='text-align: center'>Estrato</th><th style='text-align: center'>Quantidade</th></tr>");
                $("#tabelaEstratos").append("<tr><td>A1</td><td>" + estratos.A1 + "</td></tr>");
                $("#tabelaEstratos").append("<tr><td>A2</td><td>" + estratos.A2 + "</td></tr>");
                $("#tabelaEstratos").append("<tr><td>B1</td><td>" + estratos.B1 + "</td></tr>");
                $("#tabelaEstratos").append("<tr><td>B2</td><td>" + estratos.B2 + "</td></tr>");
                $("#tabelaEstratos").append("<tr><td>B3</td><td>" + estratos.B3 + "</td></tr>");
                $("#tabelaEstratos").append("<tr><td>B4</td><td>" + estratos.B4 + "</td></tr>");
                $("#tabelaEstratos").append("<tr><td>B5</td><td>" + estratos.B5 + "</td></tr>");
                $("#orientacoes").html("Orientações concluídas: " + obj.orientacoes);
                $("#ig").html("Índice geral: " + obj.ig);
                $("#ir").html("Índice de recadastramento: " + obj.ir);
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
        indices();
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


