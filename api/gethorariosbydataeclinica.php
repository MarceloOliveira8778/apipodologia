<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'get') {

    $token = filter_input(INPUT_GET, 'token');
    $clinica = filter_input(INPUT_GET, 'clinica');
    $procedimento = filter_input(INPUT_GET, 'procedimento');
    $dataagenda = dataOuNulo(filter_input(INPUT_GET, 'dataagenda'));

    if($token && $clinica && $dataagenda) {

        $usuario = getUsuarioByToken($token, $pdo);


        if($usuario) {

            $diasemana_numero = date('w', formataDataPadraoUSATime($dataagenda)) + 1;

            $intervaloagenda = getTempoProcedimento($procedimento, $pdo);

            $linhasql = "select ah.* from pre_agendas_horarios ah inner join pre_agendas pa on pa.id = ah.cod_preagendas where pa.cod_clinica = ".$clinica." and ah.diasemana = ".$diasemana_numero." order by hora_inicio";

            $sql = $pdo->query($linhasql);
            if($sql->rowCount() > 0) {
                $data = $sql->fetchAll(PDO::FETCH_ASSOC);

                foreach($data as $item) {

                    $horainiciobd = (int)$item['hora_inicio'];
                    $horafimbd = (int)$item['hora_fim'];

                    $horainiciohorario = $horainiciobd;
                    $horafimhorario = $horainiciohorario + $intervaloagenda;

                    while($horafimhorario <= $horafimbd) {

                        $horapraserinicio = getHoraInicio($dataagenda, $horainiciohorario, $horafimhorario, $clinica, $pdo);

                        if($horapraserinicio === $horainiciohorario) {

                            $descricaohorario = "Das ".labelHora($horainiciohorario)." às ".labelHora($horafimhorario);

                            $array['result'][] = [
                                'id' => $item['id'],
                                'descricao' => $descricaohorario,
                                'hora_inicio' => $horainiciohorario,
                                'hora_fim' => $horafimhorario
                            ];    

                            $horainiciohorario = $horafimhorario;

                        } else {

                            $horainiciohorario = $horapraserinicio;

                        }                       

                        $horafimhorario = $horainiciohorario + $intervaloagenda;

                    }

                }
            }
        } else {
            $array['error'] = 'Token inválido.';
        }  
        
        
    } else {
        $array['error'] = 'Parâmetros obrigatórios não enviados.';
    }   

} else {
    $array['error'] = 'Entrada não permitida.';
}

require('../retorno.php');

?>