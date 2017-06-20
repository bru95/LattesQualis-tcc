<?php

/**
 * Description of QualisDAO
 *
 * @author Bruna
 */
include_once 'DAO.php';

class QualisDAO {

    var $colecao;
    var $dao;

    function QualisDAO() {
        $this->colecao = "qualis";
        $this->dao = new DAO();
    }

    function buscaPubli() {
        $filter = [];
        $options = ['projection' => ['_id' => 0, "conferencias2010_2012" => 1, "conferencias2013_2016" => 1]];
        $rows = json_decode($this->dao->select($this->colecao, $filter, $options), TRUE);
        $conferencias["conferencias2010_2012"] = $rows[0]["conferencias2010_2012"];
        $conferencias["conferencias2013_2016"] = $rows[1]["conferencias2013_2016"];
        return $conferencias;
    }

    function insereQualis($dados) {
        $this->dao->insereUmDoc($this->colecao, $dados);
    }

    function qualisConferencia($conferencia, $ano) {
        if ($ano >= 2010 && $ano <= 2012) {
            $doc = "conferencias2010_2012";
        } else {
            $doc = "conferencias2013_2016";
        }
        $filter = ["$doc.conferencia" => "$conferencia"];
        $options = [
            'projection' => ['_id' => 0, "$doc" => ['$elemMatch' => ['conferencia' => "$conferencia"]]],
        ];
        $rows = json_decode($this->dao->select($this->colecao, $filter, $options), TRUE);
        if (!empty($rows)) {
            return $rows[0]["$doc"][0]['estrato'];
        } else {
            return null;
        }
    }

    function qualisPeriodicos($issn, $ano) {
        if ($ano >= 2010 && $ano <= 2014) {
            $doc = "periodicos" . $ano;
        } else {
            $doc = "periodicos2015";
        }
        $filter = ["$doc.ISSN" => "$issn"];
        $options = [
            'projection' => ['_id' => 0, "$doc" => ['$elemMatch' => ['ISSN' => "$issn"]]],
        ];
        $rows = json_decode($this->dao->select($this->colecao, $filter, $options), TRUE);
        if (!empty($rows)) {
            return $rows[0]["$doc"][0]["estrato"];
        } else {
            return null;
        }
    }

    function pesosCriteriosIndices() {
        $filter = [];
        $options = [];
        $rows = json_decode($this->dao->select("pesoQualis", $filter, $options), TRUE);
        return $rows;
    }

    function salvaPesos($a1, $a2, $b1, $b2, $b3, $b4, $b5, $orientacoes) {
        $filter = [];
        $options = ['$set' => ['A1' => $a1, 'A2' => $a2, 'B1' => $b1, 'B2' => $b2,
                'B3' => $b3, 'B4' => $b4, 'B5' => $b5, 'orientacoes' => $orientacoes,]];
        $this->dao->update("pesoQualis", $filter, $options);
    }
    
    function inserePesosQualis($a1, $a2, $b1, $b2, $b3, $b4, $b5, $orientacoes){
        $dados = ['A1' => $a1, 'A2' => $a2, 'B1' => $b1, 'B2' => $b2,
                'B3' => $b3, 'B4' => $b4, 'B5' => $b5, 'orientacoes' => $orientacoes];
        $this->dao->insereUmDoc("pesoQualis", $dados);
    }

    function verificaQualisMongo() {
        $filter = [];
        $options = [];
        $rows = $this->dao->select($this->colecao, $filter, $options);
        return sizeof(json_decode($rows));
    }
    
    function verificaPesosQualisMongo() {
        $filter = [];
        $options = [];
        $rows = $this->dao->select("pesoQualis", $filter, $options);
        return sizeof(json_decode($rows));
    }

}
