<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'get') {

    $id = filter_input(INPUT_GET, 'id');
    $token = filter_input(INPUT_GET, 'token');

    if($token) {

        $usuario = getUsuarioByToken($token, $pdo);

        if($usuario) {

            $linhasql = "";
            if($usuario['cca'] === 'AGENCIA') {
                $linhasql = "select * from processos where id = :id";
            } else {
                $linhasql = "select * from processos where id = :id and cca = '" . $usuario['cca'] . "' ";
            }

            if($id) {

                $sql = $pdo->prepare($linhasql);
                $sql->bindValue(':id', $id);
                $sql->execute();
        
                if($sql->rowCount() > 0) {
        
                    $item = $sql->fetch(PDO::FETCH_ASSOC);
        
                    $array['result'] = [
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
        
                } else {
                    $array['error'] = 'ID inexistente.';
                }
        
            } else {
                $array['error'] = 'ID não enviada.';
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