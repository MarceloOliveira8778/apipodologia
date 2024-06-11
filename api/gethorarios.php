<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'get') {

    $token = filter_input(INPUT_GET, 'token');
    $preagenda = filter_input(INPUT_GET, 'preagenda');
    $intervalo = filter_input(INPUT_GET, 'intervalo');

    if($token && $preagenda) {

        $usuario = getUsuarioByToken($token, $pdo);
        $intervaloagenda = $intervalo ? (int)$intervalo : 60;

        if($usuario) {

            $linhasql = "select ah.* from pre_agendas_horarios ah where ah.cod_preagendas = " . $preagenda;

            $sql = $pdo->query($linhasql);
            if($sql->rowCount() > 0) {
                $data = $sql->fetchAll(PDO::FETCH_ASSOC);

                foreach($data as $item) {

                    $horainiciobd = (int)$item['hora_inicio'];
                    $horafimbd = (int)$item['hora_fim'];

                    $horainiciohorario = $horainiciobd;
                    $horafimhorario = $horainiciohorario + $intervaloagenda;

                    while($horafimhorario <= $horafimbd) {

                        $array['result'][] = [
                            'id' => $item['id'],
                            'diasemana' => $item['diasemana'],
                            'hora_inicio' => $horainiciohorario,
                            'hora_fim' => $horafimhorario
                        ];

                        $horainiciohorario = $horafimhorario;
                        $horafimhorario = $horainiciohorario + $intervaloagenda;

                    }

                }
            }
        } else {
            $array['error'] = 'Token inválido.';
        }  
        
        
    } else {
        $array['error'] = 'Token ou pré-agenda não enviados.';
    }   

} else {
    $array['error'] = 'Entrada não permitida.';
}

require('../retorno.php');

?>