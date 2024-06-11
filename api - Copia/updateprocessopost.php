<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'post') {

    $token = filter_input(INPUT_POST, 'token');

    if($token) {

        $usuario = getUsuarioByToken($token, $pdo);

        if($usuario) {

            $id = filter_input(INPUT_POST, 'id');
            $nome = filter_input(INPUT_POST, 'nome');
            $cpf = filter_input(INPUT_POST, 'cpf');
            $modalidade = filter_input(INPUT_POST, 'modalidade');
            $valorfinanciado = filter_input(INPUT_POST, 'valorfinanciado');
            $situacao = filter_input(INPUT_POST, 'situacao');
            $observacao = filter_input(INPUT_POST, 'observacao');
            $cca = filter_input(INPUT_POST, 'cca');
            $assinado = filter_input(INPUT_POST, 'assinado');

            if($id && $nome && $situacao) {

                $sql = $pdo->prepare("select * from processos where id = :id");
                $sql->bindValue(':id', $id);
                $sql->execute();

                if($sql->rowCount() > 0) {

                    $sql = $pdo->prepare("update processos set nome = :nome, cpf = :cpf, modalidade = :modalidade, 
                            valorfinanciado = :valorfinanciado, situacao = :situacao, observacao = :observacao, 
                            assinado = :assinado where id = :id and cca = :cca ");
                    $sql->bindValue(':id', $id);
                    $sql->bindValue(':nome', $nome);
                    $sql->bindValue(':cpf', $cpf);
                    $sql->bindValue(':modalidade', $modalidade);
                    $sql->bindValue(':valorfinanciado', $valorfinanciado);
                    $sql->bindValue(':situacao', $situacao);
                    $sql->bindValue(':observacao', $observacao);
                    $sql->bindValue(':assinado', $assinado);
                    $sql->bindValue(':cca', $cca);
                    $sql->execute();

                    $array['result'] = [
                        'id' => $id,
                        'nome' => $nome,
                        'cpf' => $cpf,
                        'modalidade' => $modalidade,
                        'valorfinanciado' => $valorfinanciado,
                        'situacao' => $situacao,
                        'observacao' => $observacao,
                        'assinado' => $assinado,
                        'cca' => $cca
                    ];

                } else {
                    $array['error'] = 'ID inexistente.';
                }

            } else {
                $array['error'] = 'Campos requeridos não enviados: id, nome e situação.';
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