<?php

/**
 * Description of qualis
 *
 * @author Bruninha
 */
ini_set('max_execution_time', 300);
include_once $_SERVER["DOCUMENT_ROOT"] . "/TCCBruna/dados/DAO/QualisDAO.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/TCCBruna/dados/DAO/SimilaridadeDAO.php";

class Qualis {

    var $dao;
    var $conferencias;

    function Qualis() {
        $this->dao = new QualisDAO();
        $this->conferencias = $this->dao->buscaPubli();
    }

    function primeiroAcesso() {
        if ($this->dao->verificaQualisMongo() == 0) {
            $this->insereQualis("../dadosMongo/qualis/conferencias/", "evento");
            $this->insereQualis("../dadosMongo/qualis/periodicos/", "periodico");
        }
        if ($this->dao->verificaPesosQualisMongo() == 0) {
            $this->dao->inserePesosQualis(1, 0.85, 0.7, 0.5, 0.2, 0.1, 0.05, 0.3);
        }
    }

    function insereQualis($dir, $nomeArray) {
        $arquivos = scandir($dir);
        foreach ($arquivos as $key => $value) {
            if ($value != '.' && $value != '..') {
                $arquivo = file_get_contents($dir . $value);
                $xml = simplexml_load_string($arquivo);
                $json = json_encode($xml);
                $array = json_decode($json, TRUE);
                $nomeArq = substr($value, 0, strlen($value) - 4);
                $dados = [$nomeArq => $array[$nomeArray]];
                $this->dao->insereQualis($dados);
            }
        }
    }

    /**
     * Cria um array com o nome do evento, o ano, o titulo do trabalho, e o
     * estrato dos trabalhos publicados em conferencias
     * @param array $trabalhos trabalhos que se deseja classificar
     */
    function classificaConferencias($trabalhos) {
        $classificacoesC = [];
        if (isset($trabalhos[0])) {
            foreach ($trabalhos as $value) {
                $classificacoesC[] = $this->classificaConferencia($value);
            }
        } else if (isset($trabalhos['DETALHAMENTO-DO-TRABALHO'])) {
            $classificacoesC[] = $this->classificaConferencia($trabalhos);
        }
        return $classificacoesC;
    }

    function classificaConferencia($trabalho) {
        $estrato = $this->dao->qualisConferencia($trabalho['DETALHAMENTO-DO-TRABALHO']['NOME-DO-EVENTO'], $trabalho['DADOS-BASICOS-DO-TRABALHO']['ANO-DO-TRABALHO']);
        if ($estrato == null) {
            $similaridade = new SimilaridadeDAO();
            $estrato = $similaridade->qualisListaIgualdade($trabalho['DETALHAMENTO-DO-TRABALHO']['NOME-DO-EVENTO'], $trabalho['DADOS-BASICOS-DO-TRABALHO']['ANO-DO-TRABALHO']);
            if ($estrato == null) {
                $estratoSim = $this->simLev($trabalho['DETALHAMENTO-DO-TRABALHO']['NOME-DO-EVENTO'], $trabalho['DADOS-BASICOS-DO-TRABALHO']['ANO-DO-TRABALHO']);
                if ($estratoSim != null) {
                    $aux["EVENTO_QUALIS"] = $estratoSim['evento'];
                }
            } else {
                $estratoSim['estrato'] = $estrato;
            }
        } else {
            $estratoSim['estrato'] = $estrato;
        }
        $aux["EVENTO"] = $trabalho['DETALHAMENTO-DO-TRABALHO']['NOME-DO-EVENTO'];
        $aux["ANO"] = $trabalho['DADOS-BASICOS-DO-TRABALHO']['ANO-DO-TRABALHO'];
        $aux["TRABALHO"] = $trabalho['DADOS-BASICOS-DO-TRABALHO']['TITULO-DO-TRABALHO'];
        $aux["ESTRATO"] = trim($estratoSim['estrato']);
        return $aux;
    }

    /**
     * Cria um array com o ano, o issn, o titulo e o estrato de cada artigo
     * publicado em periodicos
     * @param array $trabalhos artigos que se deseja classificar
     */
    function classificaPeriodicos($trabalhos) {
        $classificacoesP = [];
        if (isset($trabalhos[0])) {
            foreach ($trabalhos as $value) {
                $classificacoesP[] = $this->classificaPeriodico($value);
            }
        } else if (isset($trabalhos['DETALHAMENTO-DO-ARTIGO'])) {
            $classificacoesP[] = $this->classificaPeriodico($trabalhos);
        }

        return $classificacoesP;
    }

    function classificaPeriodico($periodico) {
        $issn = substr($periodico['DETALHAMENTO-DO-ARTIGO']['ISSN'], 0, 4) . "-" .
                substr($periodico['DETALHAMENTO-DO-ARTIGO']['ISSN'], 4);
        $estrato = $this->dao->qualisPeriodicos($issn, $periodico['DADOS-BASICOS-DO-ARTIGO']['ANO-DO-ARTIGO']);
        $aux["ANO"] = $periodico['DADOS-BASICOS-DO-ARTIGO']['ANO-DO-ARTIGO'];
        $aux["PERIODICO"] = $periodico['DETALHAMENTO-DO-ARTIGO']['TITULO-DO-PERIODICO-OU-REVISTA'];
        $aux["TITULO"] = $periodico['DADOS-BASICOS-DO-ARTIGO']['TITULO-DO-ARTIGO'];
        $aux["ESTRATO"] = trim($estrato);
        return $aux;
    }

    function pegaPesos() {
        return $this->dao->pesosCriteriosIndices();
    }

    function updatePesos($a1, $a2, $b1, $b2, $b3, $b4, $b5, $orientacoes) {
        $this->dao->salvaPesos($a1, $a2, $b1, $b2, $b3, $b4, $b5, $orientacoes);
    }

    function simLev($conf, $ano) {
        if ($ano >= 2010 && $ano <= 2012) {
            $doc = "conferencias2010_2012";
        } else {
            $doc = "conferencias2013_2016";
        }
        $dados = $this->distLevenshtein($conf, $doc);
        $simLev = 1 - ($dados["lev"] / (max(strlen($conf), strlen($this->conferencias["$doc"][$dados["index"]]['conferencia']))));
        if ($simLev > 0.40) {
            $retorno['estrato'] = $this->conferencias["$doc"][$dados["index"]]['estrato'];
            $retorno['evento'] = $this->conferencias["$doc"][$dados["index"]]['conferencia'];
            return $retorno;
        } else {
            return null;
        }
    }

    function distLevenshtein($conf, $doc) {
        $lev = -1;
        $index = -1;
        for ($i = 0; $i < sizeof($this->conferencias["$doc"]); $i++) {
            $nome = levenshtein($conf, $this->conferencias["$doc"][$i]['conferencia']);
            $sigla = levenshtein($conf, $this->conferencias["$doc"][$i]['sigla']);
            if ($lev < 0 || min([$nome, $sigla]) < $lev) {
                $lev = min([$nome, $sigla]);
                $index = $i;
            }
        }
        $retorno["lev"] = $lev;
        $retorno["index"] = $index;
        return $retorno;
    }

}
