<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'get') {

    $token = filter_input(INPUT_GET, 'token');

    if($token) {

        $usuario = getUsuarioByToken($token, $pdo);

        if($usuario) {

            $linhasql = "";
            if($usuario['cca'] === 'AGENCIA') {
                $linhasql = "select * from processos ";
            } else {
                $linhasql = "select * from processos where cca = '" . $usuario['cca'] . "' ";
            }

            $sql = $pdo->query($linhasql);
            if($sql->rowCount() > 0) {
                $data = $sql->fetchAll(PDO::FETCH_ASSOC);

                foreach($data as $item) {
                    $array['result'][] = [
                        'id' => $item['id'],
                        'nome' => $item['nome'],
                        'cpf' => $item['cpf'],
                        'modalidade' => $item['modalidade'],
                        'valorfinanciado' => $item['valorfinanciado'],
                        'situacao' => $item['situacao'],
                        'observacao' => $item['observacao'],
                        'assinado' => $item['assinado'],
                        'cca' => $item['cca']
                    ];
                }
            }
        } else {
            $array['error'] = 'Token inválido.';
        }  
        
        
    } else {
        $array['error'] = 'Token não enviado.';
    }   

} else {
    $array['error'] = 'Entrada não permitida.';
}

require('../retorno.php');

?>