<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'delete') {

    //aqui não da pra usar filter_input, pois não existe INPUT_PUT, então fazemos desta forma
    parse_str(file_get_contents('php://input'), $input);

    $token = (!empty($input['token'])) ? $input['token'] : null;

    if($token) {

        $usuario = getUsuarioByToken($token, $pdo);

        if($usuario) {

            $cca = $usuario['cca'];

            $id = (!empty($input['id'])) ? $input['id'] : null;

            $id = filter_var($id);

            if($id) {

                $sql = $pdo->prepare("select * from processos where id = :id");
                $sql->bindValue(':id', $id);
                $sql->execute();

                if($sql->rowCount() > 0) {

                    $sql = $pdo->prepare("delete from processos where id = :id and cca = :cca ");
                    $sql->bindValue(':id', $id);
                    $sql->bindValue(':cca', $cca);
                    $sql->execute();

                } else {
                    $array['error'] = 'ID inexistente.';
                }

            } else {
                $array['error'] = 'ID não enviado.';
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