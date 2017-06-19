<?php

/**
 * Description of Similaridade
 *
 * @author Bruninha
 */
include_once $_SERVER["DOCUMENT_ROOT"] . "/TCCBruna/dados/DAO/SimilaridadeDAO.php";
ini_set('display_errors', 1);

class Similaridade {

    var $dao;

    function Similaridade() {
        $this->dao = new SimilaridadeDAO();
    }
    
    function insereSimilar($conferencias){
        foreach ($conferencias as $value) {
            $this->dao->insereSimilar($value['evento'], $value['estrato'], $value['ano']);
        }
    }

}
