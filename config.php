<?php

/* FUNÇÕES DE TRATAMENTO DE VARIAVEIS */
function dataOuNulo($data_tratar) {
    //cria um array
    $array = explode('/', $data_tratar);

    //garante que o array possue tres elementos (dia, mes e ano)
    if(count($array) == 3){
        $dia = (int)$array[0];
        $mes = (int)$array[1];
        $ano = (int)$array[2];

        //testa se a data é válida
        if(checkdate($mes, $dia, $ano)){
            return $data_tratar;
        }else{
            return null;
        }
    }else{
        return null;
    }
}

function labelHora($minutos_totais) {

    $horas = floor($minutos_totais / 60);
    $minutos = $minutos_totais % 60;

    return substr("00" .$horas, -2) . ":" . substr("00" .$minutos, -2);
}

function formataDataPadraoUSAString($vardata) {
    return implode('-', array_reverse(explode('/', $vardata)));
}

function formataDataPadraoUSATime($vardata) {
    return strtotime(implode('-', array_reverse(explode('/', $vardata))));
}

/* FUNÇÕES DE BANCO DE DADOS */
function getTempoProcedimento($cod_procedimento, $pdo) {

    $linhasql = "select tempo_padrao from procedimentos where id = ".$cod_procedimento;
    $sql = $pdo->query($linhasql);

    if($sql->rowCount() > 0) {

        $result = $sql->fetch();
        return $result['tempo_padrao'];

    } else {
        return 60;
    }

}

function getUsuarioByToken($token, $pdo) {

    $sql = $pdo->prepare("select * from usuarios where ativo = 'S' and id = :token");
    $sql->bindValue(':token', $token);
    $sql->execute();

    if($sql->rowCount() > 0) {

        return $sql->fetch(PDO::FETCH_ASSOC);

    } else {
        return null;
    }

}

function getHoraInicio($vardata, $varhorainicio, $varhorafim, $varcodclinica, $pdo) {
    $retorno = $varhorainicio;

    /*checando se hora inicio está contido em algum horario*/
    $linhasqlinicio = "select (hour(datahorafimreal) * 60 + minute(datahorafimreal)) as 'HoraRetorno' from vw_agendamentostodos where data_agendamento = '".formataDataPadraoUSAString($vardata)."' and '".formataDataPadraoUSAString($vardata)." ".labelHora($varhorainicio)."' BETWEEN datahorainicio and datahorafimmenos1segundo and cod_clinica = ".$varcodclinica." order by datahorafimreal";

    $sqlinicio = $pdo->query($linhasqlinicio);
    if($sqlinicio->rowCount() > 0) {
        $datainicio = $sqlinicio->fetchAll(PDO::FETCH_ASSOC);

        $retorno = (int)$datainicio[0]['HoraRetorno'];
    } else {

        /*checando se hora fim está contida em algum horario*/
        $linhasqlfim = "select (hour(datahorafimreal) * 60 + minute(datahorafimreal)) as 'HoraRetorno' from vw_agendamentostodos where data_agendamento = '".formataDataPadraoUSAString($vardata)."' and '".formataDataPadraoUSAString($vardata)." ".labelHora($varhorafim-1).":59' BETWEEN datahorainicio and datahorafimmenos1segundo and cod_clinica = ".$varcodclinica." order by datahorafimreal";

        $sqlfim = $pdo->query($linhasqlfim);
        if($sqlfim->rowCount() > 0) {
            $datafim = $sqlfim->fetchAll(PDO::FETCH_ASSOC);

            $retorno = (int)$datafim[0]['HoraRetorno'];
        }

    }

    return $retorno;
}

$db_host = 'srv654.hstgr.io';
$db_name = 'u523609754_bdpodologia';
$db_user = 'u523609754_rootpodologia';
$db_pass = 'P0d0l0G1apodoL0G4';

$pdo = new PDO("mysql:dbname=$db_name;host=$db_host", $db_user, $db_pass);

$array = [
    'error' => '',
    'result' => []
];

?>