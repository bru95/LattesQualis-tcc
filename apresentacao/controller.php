<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author Bruninha
 */
include_once '../dados/Lattes.php';
include_once '../dados/Qualis.php';
include_once '../dados/Similaridade.php';

class controller {

    var $lattes;
    var $qualis;
    var $similaridade;

    function controller() {
        $this->lattes = new Lattes();
        $this->qualis = new Qualis();
        $this->similaridade = new Similaridade();
    }

    function pegaConf($professor) {
        $trabalhos = $this->lattes->pegaConferencias($professor);
        return json_encode($this->qualis->classificaConferencias($trabalhos));
    }

    function pegaPer($professor) {
        $trabalhos = $this->lattes->pegaPeriodicos($professor);
        return json_encode($this->qualis->classificaPeriodicos($trabalhos));
    }

    function indices($professor, $ano, $classificacoesC, $classificacoesP) {
        if ($classificacoesC == null) {
            $classificacoesC = $this->qualis->classificaConferencias($this->lattes->pegaConferencias($professor));
        }
        if ($classificacoesP == null) {
            $classificacoesP = $this->qualis->classificaPeriodicos($this->lattes->pegaPeriodicos($professor));
        }
        $retorno['orientacoes'] = $this->lattes->orientacoesConcluidas($professor, $ano);
        $retorno['qtd'] = $this->lattes->contaEstratoPeriodo(array_merge($classificacoesP, $classificacoesC), $ano);
        $pesos = $this->qualis->pegaPesos();
        $retorno['ig'] = $this->lattes->calculaIG($retorno['qtd'], $pesos);
        $retorno['ir'] = $this->lattes->calculaIR($classificacoesP, $classificacoesC, $retorno['orientacoes'], $ano, $pesos);
        return json_encode($retorno);
    }

    function filtrarConf($professor, $ano, $anoF, $estrato, $trabs) {
        $trabs = json_decode($trabs, TRUE);
        //$trabs = $this->qualis->classificaConferencias($this->lattes->pegaConferencias($professor));
        if ($estrato != null) {
            $trabs = $this->removeEstrato($trabs, trim(strtoupper($estrato)));
        }
        if ($ano != null) {
            $trabs = $this->removeAno($trabs, $ano, $anoF);
        }
        return json_encode($trabs);
    }

    function filtrarPer($professor, $ano, $anoF, $estrato) {
        $trabs = $this->qualis->classificaPeriodicos($this->lattes->pegaPeriodicos($professor));
        if ($estrato != null) {
            $trabs = $this->removeEstrato($trabs, trim(strtoupper($estrato)));
        }
        if ($ano != null) {
            $trabs = $this->removeAno($trabs, $ano, $anoF);
        }
        return json_encode($trabs);
    }

    function removeEstrato($trabs, $estrato) {
        $trabalhos = [];
        foreach ($trabs as $value) {
            if ((array_search($estrato, $value)) != false) {
                $trabalhos[] = $value;
            }
        }
        return $trabalhos;
    }

    /**
     * TENTAR FAZER NO BD
     * @param type $trabs
     * @param type $ano
     * @return type
     */
    function removeAno($trabs, $ano, $anoF) {
        $trabalhos = [];
        if (!strcmp($anoF, "") || $anoF == null) {
            $anoF = $ano;
        }
        foreach ($trabs as $value) {
            if ($value['ANO'] >= $ano && $value['ANO'] <= $anoF) {
                $trabalhos[] = $value;
            }
        }
        return $trabalhos;
    }

    function dadosBasicos($professor) {
        return $this->lattes->pegaDadosBasicos($professor);
    }

    function pesosIndice() {
        return json_encode($this->qualis->pegaPesos());
    }

    function alteraPesos($a1, $a2, $b1, $b2, $b3, $b4, $b5, $orientacoes) {
        $this->qualis->updatePesos($a1, $a2, $b1, $b2, $b3, $b4, $b5, $orientacoes);
    }

    function armazenaSimilares($eventos) {
        $conferencias = json_decode(json_encode($eventos), TRUE);
        $this->similaridade->salvaSimilares($conferencias);
    }

    function professores() {
        return json_encode($this->lattes->nomeProfessores());
    }

    function trabalhosProfessores() {
        $trabalhos = $this->lattes->trabalhosProfessores();
        foreach ($trabalhos['conf'] as $key => $value) {
            $classificados['conf'][$key] = $this->qualis->classificaConferencias($value);
        }
        foreach ($trabalhos['per'] as $key => $value) {
            $classificados['per'][$key] = $this->qualis->classificaPeriodicos($value);
        }
        return json_encode($classificados);
    }

    function filtrarTrabsProfs($ano, $anoF, $estrato, $professor, $categoria, $conferencias, $periodicos) {
        $conf = json_decode($conferencias, TRUE);
        $per = json_decode($periodicos, TRUE);
        if (!strcmp($professor, "0")) {
            $trabalhos['conf'] = $conf;
            $trabalhos['per'] = $per;
        } else {
            if (isset($conf["$professor"])) {
                $trabalhos['conf'][$professor] = $conf["$professor"];
            } else {
                $trabalhos['conf'][$professor] = [];
            }
            if (isset($per["$professor"])) {
                $trabalhos['per'][$professor] = $per["$professor"];
            } else {
                $trabalhos['per'][$professor] = [];
            }
        }
        if (!strcmp($categoria, "1")) {
            $trabalhos['per'] = [];
        } else if (!strcmp($categoria, "2")) {
            $trabalhos['conf'] = [];
        }
        foreach ($trabalhos['conf'] as $key => $value) {
            if ($ano != null) {
                $trabalhos['conf'][$key] = $this->removeAno($trabalhos['conf'][$key], $ano, $anoF);
            }
            if ($estrato != null) {
                $trabalhos['conf'][$key] = $this->removeEstrato($trabalhos['conf'][$key], trim(strtoupper($estrato)));
            }
        }
        foreach ($trabalhos['per'] as $key => $value) {
            if ($ano != null) {
                $trabalhos['per'][$key] = $this->removeAno($trabalhos['per'][$key], $ano, $anoF);
            }
            if ($estrato != null) {
                $trabalhos['per'][$key] = $this->removeEstrato($trabalhos['per'][$key], trim(strtoupper($estrato)));
            }
        }
        return json_encode($trabalhos);
    }

    function IRProfessores($ano, $conferencias, $periodicos) {
        $conf = json_decode($conferencias, TRUE);
        $per = json_decode($periodicos, TRUE);
        $nomes = $this->lattes->nomeProfessores();
        foreach ($nomes as $nome) {
            if (!isset($conf[$nome["DADOS-GERAIS"]["NOME-COMPLETO"]])) {
                $conf[$nome["DADOS-GERAIS"]["NOME-COMPLETO"]] = [];
            }
            if (!isset($per[$nome["DADOS-GERAIS"]["NOME-COMPLETO"]])) {
                $per[$nome["DADOS-GERAIS"]["NOME-COMPLETO"]] = [];
            }
            $indices[$nome["DADOS-GERAIS"]["NOME-COMPLETO"]] = json_decode($this->indices($nome["DADOS-GERAIS"]["NOME-COMPLETO"], $ano, $conf[$nome["DADOS-GERAIS"]["NOME-COMPLETO"]], $per[$nome["DADOS-GERAIS"]["NOME-COMPLETO"]]));
        }
        return json_encode($indices);
    }

    function atualizarCurriculosLattes() {
        $this->lattes->removeColecaoLattes();
        $this->lattes->processaLattes("../dadosMongo/lattes/");
    }
    function dadosPrimeiroAcesso(){
        $this->lattes-> primeiroAcesso();
        $this->qualis->primeiroAcesso();
    }

}
