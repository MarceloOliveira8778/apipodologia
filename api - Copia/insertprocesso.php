<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'post') {

    $token = filter_input(INPUT_POST, 'token');

    if($token) {

        $usuario = getUsuarioByToken($token, $pdo);

        if($usuario) {

            $nome = filter_input(INPUT_POST, 'nome');
            $cpf = filter_input(INPUT_POST, 'cpf');
            $modalidade = filter_input(INPUT_POST, 'modalidade');
            $valorfinanciado = filter_input(INPUT_POST, 'valorfinanciado');
            $situacao = filter_input(INPUT_POST, 'situacao');
            $observacao = filter_input(INPUT_POST, 'observacao');
            $cca = $usuario['cca'];

            if($nome && $situacao) {

                $sql = $pdo->prepare("insert into processos (nome, cpf, modalidade, valorfinanciado, 
                            situacao, observacao, cca) values (:nome, :cpf, :modalidade, :valorfinanciado, 
                            :situacao, :observacao, :cca)");
                $sql->bindValue(':nome', $nome);
                $sql->bindValue(':cpf', $cpf);
                $sql->bindValue(':modalidade', $modalidade);
                $sql->bindValue(':valorfinanciado', $valorfinanciado);
                $sql->bindValue(':situacao', $situacao);
                $sql->bindValue(':observacao', $observacao);
                $sql->bindValue(':cca', $cca);
                $sql->execute();

                $id = $pdo->lastInsertId();

                $array['result'] = [
                    'id' => $id,
                    'nome' => $nome,
                    'cpf' => $cpf,
                    'modalidade' => $modalidade,
                    'valorfinanciado' => $valorfinanciado,
                    'situacao' => $situacao,
                    'observacao' => $observacao,
                    'assinado' => false,
                    'cca' => $cca
                ];

            } else {
                $array['error'] = 'Campos requeridos não enviados: nome e situação.';
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