<?php

/**
 * Description of Siape
 *
 * @author Bruninha
 */
class SiapeDAO {

    var $dao;

    function SiapeDAO() {
        $this->dao = new DAO();
    }

    function insereSiapes() {
        $xml = simplexml_load_file("./dadosMongo/siape/siape.xml");
        foreach ($xml->docente as $docente) {
            $nome = explode(" ", $docente->nome);
            $login = $this->removeAcento($nome[0] . $nome[sizeof($nome) - 1]);
            $nomeAcento = $this->removeAcento($docente->nome);
            $this->insereMongo(['siape' => "$docente->siape", 'nome' => "$nomeAcento", 'login' => "$login"]);
        }
    }

    function insereMongo($dados) {
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($dados);
        $manager->executeBulkWrite("tccBruna.siape", $bulk);
    }

    /**
     * https://forum.imasters.com.br/topic/502244-remover-acentos-em-strings-php/
     * @param type $string
     * @return type
     */
    function removeAcento($string) {
        return preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $string));
    }

}
