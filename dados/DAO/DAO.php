<?php

/**
 * Description of DAO
 *
 * @author Bruninha
 */
ini_set('display_errors', 1);

class DAO {

    var $manager;
    var $bd;

    function DAO() {
        $this->manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $this->bd = "tccBruna";
    }

    function insereUmDoc($colecao, $dados) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($dados);
        $this->manager->executeBulkWrite("$this->bd.$colecao", $bulk);
    }

    function select($colecao, $filter, $options) {
        $query = new MongoDB\Driver\Query($filter, $options);
        return json_encode($this->manager->executeQuery("$this->bd.$colecao", $query)->toArray());
    }

    function update($colecao, $filter, $options) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, $options);
        $this->manager->executeBulkWrite("$this->bd.$colecao", $bulk);
    }

    function delete($condicao, $colecao) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete($condicao);
        $this->manager->executeBulkWrite("$this->bd.$colecao", $bulk);
    }

}
