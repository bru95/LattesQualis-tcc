<?php

include_once './controller.php';

$acao = $_POST['acao'];

$controller = new controller();

switch ($acao) {
    case "confDocente":
        echo $controller->pegaConf($_POST['nome']);
        break;
    case "perDocente":
        echo $controller->pegaper($_POST['nome']);
        break;
    case "indicesDocente":
        echo $controller->indices($_POST['nome'], $_POST['ano'], json_decode(json_encode($_POST['conferencias']), TRUE), null);
        break;
    case "filtraConf":
        echo $controller->filtrarConf($_POST['nome'], $_POST['ano'], $_POST['anoF'], $_POST['estrato'], json_encode($_POST['conferencias']));
        break;
    case "filtraPer":
        echo $controller->filtrarPer($_POST['nome'], $_POST['ano'], $_POST['anoF'], $_POST['estrato']);
        break;
    case "dadosProf":
        echo $controller->dadosBasicos($_POST['nome']);
        break;
    case "pesosIndices":
        echo $controller->pesosIndice();
        break;
    case "salvaPesos":
        echo $controller->alteraPesos($_POST['A1'], $_POST['A2'], $_POST['B1'], $_POST['B2'], $_POST['B3'], $_POST['B4'], $_POST['B5'], $_POST['orientacoes']);
        break;
    case "inserirSimilares":
        $controller->insereSimilares($_POST['conferencias']);
        break;
    case "nomeProfessores":
        echo $controller->professores();
        break;
    case "trabalhos":
        echo $controller->trabalhosProfessores();
        break;
    case "filtrarTrabalhos":
        echo $controller->filtrarTrabsProfs($_POST['ano'], $_POST['anoF'], $_POST['estrato'], $_POST['professor'], $_POST['categoria'], json_encode($_POST['conferencias']), json_encode($_POST['periodicos']));
        break;
    case "indicesProfessores":
        echo $controller->IRProfessores($_POST['ano'], json_encode($_POST['conferencias']), json_encode($_POST['periodicos']));
        break;
    case "atualizarLattes":
        $controller->atualizarCurriculosLattes();
        echo "";
        break;
}

