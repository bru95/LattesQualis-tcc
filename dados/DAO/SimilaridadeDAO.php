<?php

/**
 * Description of similaridadeDAO
 *
 * @author Bruna
 */
include_once 'DAO.php';

class SimilaridadeDAO {

    var $colecao;
    var $dao;

    function SimilaridadeDAO() {
        $this->colecao = "similaridade";
        $this->dao = new DAO();
    }

    function insereSimilar($evento, $qualis, $ano) {
        $dados = ["evento" => $evento, "estrato" => $qualis, "ano" => $ano];
        $this->dao->insereUmDoc($this->colecao, $dados);
    }

    function qualisListaIgualdade($conferencia, $ano) {
        $filter = ["evento" => "$conferencia", "ano" => $ano];
        $options = [];
        $rows = json_decode($this->dao->select("similaridade", $filter, $options), TRUE);
        if (!empty($rows)) {
            return $rows[0]['estrato'];
        } else {
            return null;
        }
    }

}
