<?php

/**
 * Description of Lattes
 *
 * @author Bruninha
 */
include_once $_SERVER["DOCUMENT_ROOT"] . "/TCCBruna/dados/DAO/LattesDAO.php";

class Lattes {

    var $dao;

    function Lattes() {
        $this->dao = new LattesDAO();
    }
    
    function primeiroAcesso(){
        if($this->dao->verificaLattesMongo() == 0){
            $this->processaLattes("../dadosMongo/lattes/");
        }
    }

    /**
     * Insere arquivos lattes na base de dados
     * @param type $dir diretório dos arquivos .xml
     */
    function processaLattes($dir) {
        $arquivos = scandir($dir);
        foreach ($arquivos as $key => $value) {
            if ($value != '.' && $value != '..') {
                $arquivo = file_get_contents($dir . $value);
                $xml = simplexml_load_string($arquivo);
                $json = json_encode($xml);
                $array = json_decode($json, TRUE);
                $arraySemAtr = $this->removeChaveAtributos($array);
                $arraySemAtr['DADOS-GERAIS']['NOME-COMPLETO'] = $this->removeAcento($arraySemAtr['DADOS-GERAIS']['NOME-COMPLETO']);
                $this->dao->insereLattes($arraySemAtr);
            }
        }
    }

    function removeColecaoLattes() {
        $this->dao->deleteColLattes([]);
    }

    /**
     * https://forum.imasters.com.br/topic/502244-remover-acentos-em-strings-php/
     * @param type $string
     * @return type
     */
    function removeAcento($string) {
        return preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $string));
    }

    /**
     * Remove a chave 'attributes'
     * @param type $array
     * @return type
     */
    function removeChaveAtributos($array) {
        $novo = array();
        foreach ($array as $key => $value) {
            if (!strcasecmp($key, "@attributes")) {
                $novo = $value;
            } else if (is_array($value)) {
                $novo[$key] = $this->removeChaveAtributos($value);
            } else {
                $novo[$key] = $value;
            }
        }
        return $novo;
    }

    function pegaConferencias($professor) {
        return $this->dao->conferenciasProf($professor);
    }

    function pegaPeriodicos($professor) {
        return $this->dao->periodicosProf($professor);
    }

    function calculaIG($quantidades, $pesos) {
        $ig = 0;
        foreach ($pesos[0] as $key => $value) {
            if (strcmp($key, "orientacoes") != 0 && strcmp($key, "_id") != 0) {
                $ig = $ig + ($quantidades[$key] * $value);
            }
        }
        return $ig;
    }

    function contaEstratoPeriodo($publicacoes, $ano) {
        $ano2 = $ano - 3;
        $quantidades = ["A1" => 0, "A2" => 0, "B1" => 0, "B2" => 0, "B3" => 0, "B4" => 0, "B5" => 0];
        foreach ($publicacoes as $value) {
            if (isset($quantidades[$value['ESTRATO']]) && $value['ANO'] >= $ano2 && $value['ANO'] <= $ano) {
                $quantidades[$value['ESTRATO']] ++;
            }
        }
        return $quantidades;
    }

    function calculaIR($publicacoesP, $publicacoesC, $qtdOrientacoes, $ano, $pesos) {
        $qtdP = $this->contaEstratoPeriodo($publicacoesP, $ano);
        $qtdC = $this->contaEstratoPeriodo($publicacoesC, $ano);
        $ig = 0;
        foreach ($pesos[0] as $key => $value) {
            if (strcmp($key, "orientacoes") != 0 && strcmp($key, "_id") != 0) {
                $ig = $ig + ($qtdC[$key] * $value) + ($qtdP[$key] * ($value * 1.5));
            } else if (strcmp($key, "_id") != 0) {
                $io = $qtdOrientacoes * $value;
            }
        }
        return $ig + $io;
    }

    function orientacoesConcluidas($professor, $ano) {
        $json = $this->dao->orientacoesConcluidasMestrado($professor);
        $rows = json_decode($json, TRUE);
        if (empty($rows)) {
            return 0;
        } else {
            if (isset($rows[0]['OUTRA-PRODUCAO']['ORIENTACOES-CONCLUIDAS']['ORIENTACOES-CONCLUIDAS-PARA-MESTRADO'])) {
                return $this->quatidadeOrientacoes($rows[0]['OUTRA-PRODUCAO']['ORIENTACOES-CONCLUIDAS']['ORIENTACOES-CONCLUIDAS-PARA-MESTRADO'], $ano);
            } else {
                return 0;
            }
        }
    }

    function quatidadeOrientacoes($orientacoes, $ano) {
        $ano2 = $ano - 3;
        $qtd = 0;
        if (isset($orientacoes[0])) {
            foreach ($orientacoes as $value) {
                if ($value['DADOS-BASICOS-DE-ORIENTACOES-CONCLUIDAS-PARA-MESTRADO']['ANO'] >= $ano2 &&
                        $value['DADOS-BASICOS-DE-ORIENTACOES-CONCLUIDAS-PARA-MESTRADO']['ANO'] <= $ano) {
                    $qtd++;
                }
            }
        } else {
            if ($orientacoes['DADOS-BASICOS-DE-ORIENTACOES-CONCLUIDAS-PARA-MESTRADO']['ANO'] >= $ano2 &&
                    $orientacoes['DADOS-BASICOS-DE-ORIENTACOES-CONCLUIDAS-PARA-MESTRADO']['ANO'] <= $ano) {
                $qtd++;
            }
        }
        return $qtd;
    }

    function pegaDadosBasicos($professor) {
        return $this->dao->dadosBasicos($professor);
    }

    function trabalhosProfessores() {
        $profs = json_decode($this->dao->professores(), TRUE);
        foreach ($profs as $value) {
            $conferencias[$value["DADOS-GERAIS"]["NOME-COMPLETO"]] = $this->dao->conferenciasProf($value["DADOS-GERAIS"]["NOME-COMPLETO"]);
            $periodicos[$value["DADOS-GERAIS"]["NOME-COMPLETO"]] = $this->dao->periodicosProf($value["DADOS-GERAIS"]["NOME-COMPLETO"]);
        }
        $trabalhos["conf"] = $conferencias;
        $trabalhos["per"] = $periodicos;
        return $trabalhos;
    }

    function nomeProfessores() {
        return json_decode($this->dao->professores(), TRUE);
    }

}
