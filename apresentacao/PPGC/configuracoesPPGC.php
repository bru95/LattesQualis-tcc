<!DOCTYPE html>
<?php
include '../verificaLogin.php';
?>
<style>
    #labelPer{
        margin-left: 20px;
    }

    #imagemCarregando{
        text-align: center;
    }
    
    .panel{
        width: 90%;
        margin: auto;
    }
</style>
<html>
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
    <body onload="marcaOpcaoMenu(); pegaPesos();" >
        <div class="modal fade" id="modalSucesso" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Aguarde</h4>
                    </div>
                    <div class="modal-body">
                        <div id="imagemCarregando">
                            <img src='../../imagens/carregando.gif'>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php
        require 'menuPPGC.php';
        ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <form class="form-horizontal" id="formPesos">
                    <div class="form-group">
                        <label for="orientacoes" class="col-sm-2 control-label">Orientações concluídas</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="orientacoes" name="orientacoes">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="A1" class="col-sm-2 control-label">A1</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="A1" name="A1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="A2" class="col-sm-2 control-label">A2</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="A2" name="A2">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="B1" class="col-sm-2 control-label">B1</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="B1" name="B1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="B2" class="col-sm-2 control-label">B2</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="B2" name="B2">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="B3" class="col-sm-2 control-label">B3</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="B3" name="B3">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="B4" class="col-sm-2 control-label">B4</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="B4" name="B4">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="B5" class="col-sm-2 control-label">B5</label>
                        <div class="col-sm-2">
                            <input type="number" step="0.01" class="form-control" id="B5" name="B5">
                        </div>
                    </div>
                    <input type="hidden" value="salvaPesos" name="acao">
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="button" class="btn btn-success" value="Salvar" onclick="salvaPesos()">
                        </div>
                    </div>
                </form>
                <h6 id="labelPer">*Periódicos tem acréscimo de 50% no peso</h6>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <p>Para atualizar os currículos lattes na base de dados, adicione ou remova um arquivo XML do diretório dadosMongo/lattes, e após isso clique no botão abaixo.</p>
                <input type="button" class="btn btn-default" value="Atualizar Lattes" onclick="atualizarLattes()">
            </div>
        </div>
    </body>
</html>
<script>
    function marcaOpcaoMenu() {
        $('#configuracoes').addClass('active');
    }

    function pegaPesos() {
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            dataType: "json",
            data: {'acao': 'pesosIndices'},
            success: function (obj) {
                for (var i in obj[0]) {
                    if (i != "_id") {
                        $("#" + i).val(obj[0][i]);
                    }
                }
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function salvaPesos() {
        $("#modalSucesso").modal("show");
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            data: $("#formPesos").serialize(),
            success: function (obj) {
                pegaPesos();
                $("#modalSucesso").modal("hide");
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    function atualizarLattes() {
        $("#modalSucesso").modal("show");
        $.ajax({
            url: '../ajax.php',
            type: 'post',
            data: {'acao': 'atualizarLattes'},
            success: function () {
                $("#modalSucesso").modal("hide");
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

</script>

