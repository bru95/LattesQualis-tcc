<?php

/**
 * Description of LattesDAO
 *
 * @author Bruna
 */
ini_set('display_errors', 1);
include_once 'DAO.php';

class LattesDAO {

    var $colecao;
    var $dao;

    function LattesDAO() {
        $this->colecao = "lattes";
        $this->dao = new DAO();
    }

    function insereLattes($dados) {
        $this->dao->insereUmDoc($this->colecao, $dados);
    }
    
    function deleteColLattes($condicao){
        $this->dao->delete($condicao,"lattes");
    }

    function periodicosProf($professor) {
        $rgx = new MongoDB\BSON\Regex("$professor", "i");
        $regex = json_decode(json_encode($rgx->jsonSerialize()), TRUE);
        $filter = ['DADOS-GERAIS.NOME-COMPLETO' => $regex];
        $options = [
            'projection' => ['_id' => 0,
                'PRODUCAO-BIBLIOGRAFICA.ARTIGOS-PUBLICADOS' => 1]
        ];
        $rows = json_decode($this->dao->select($this->colecao, $filter, $options), TRUE);
        if (isset($rows[0]['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-PUBLICADOS']['ARTIGO-PUBLICADO'])) {
            return $rows[0]['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-PUBLICADOS']['ARTIGO-PUBLICADO'];
        } else {
            return [];
        }
    }

    function conferenciasProf($professor) {
        $rgx = new MongoDB\BSON\Regex("$professor", "i");
        $regex = json_decode(json_encode($rgx->jsonSerialize()), TRUE);
        $filter = ['DADOS-GERAIS.NOME-COMPLETO' => $regex];
        $options = [
            'projection' => ['_id' => 0,
                'PRODUCAO-BIBLIOGRAFICA.TRABALHOS-EM-EVENTOS.TRABALHO-EM-EVENTOS' => 1]
        ];
        $rows = json_decode($this->dao->select($this->colecao, $filter, $options), TRUE);
        if (isset($rows[0]['PRODUCAO-BIBLIOGRAFICA']['TRABALHOS-EM-EVENTOS']['TRABALHO-EM-EVENTOS'])) {
            return $rows[0]['PRODUCAO-BIBLIOGRAFICA']['TRABALHOS-EM-EVENTOS']['TRABALHO-EM-EVENTOS'];
        } else {
            return [];
        }
    }

    function orientacoesConcluidasMestrado($professor) {
        $rgx = new MongoDB\BSON\Regex("$professor", "i");
        $regex = json_decode(json_encode($rgx->jsonSerialize()), TRUE);
        $filter = ['DADOS-GERAIS.NOME-COMPLETO' => $regex];
        $options = [
            'projection' => ['_id' => 0,
                'OUTRA-PRODUCAO.ORIENTACOES-CONCLUIDAS.ORIENTACOES-CONCLUIDAS-PARA-MESTRADO' => 1]
        ];
        return $this->dao->select($this->colecao, $filter, $options);
    }

    function dadosBasicos($professor) {
        $rgx = new MongoDB\BSON\Regex("$professor", "i");
        $regex = json_decode(json_encode($rgx->jsonSerialize()), TRUE);
        $filter = ['DADOS-GERAIS.NOME-COMPLETO' => $regex];
        $options = [
            'projection' => ['_id' => 0,
                'DADOS-GERAIS' => 1]
        ];
        return $this->dao->select($this->colecao, $filter, $options);
    }

    function professores() {
        $filter = [];
        $options = [
            'projection' => ['_id' => 0,
                'DADOS-GERAIS.NOME-COMPLETO' => 1],
            'sort' => ['DADOS-GERAIS.NOME-COMPLETO' => 1]
        ];
        return $this->dao->select($this->colecao, $filter, $options);
    }
    
    function verificaLattesMongo(){
        $filter = [];
        $options = [];
        $rows = $this->dao->select($this->colecao, $filter, $options);
        return sizeof(json_decode($rows));
    }

}
