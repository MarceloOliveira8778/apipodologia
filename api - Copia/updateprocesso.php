<?php 

require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);

if($method === 'put') {

    //aqui não da pra usar filter_input, pois não existe INPUT_PUT, então fazemos desta forma
    parse_str(file_get_contents('php://input'), $input);

    $token = (!empty($input['token'])) ? $input['token'] : null;

    if($token) {

        $usuario = getUsuarioByToken($token, $pdo);

        if($usuario) {

            $id = (!empty($input['id'])) ? $input['id'] : null;
            $nome = (!empty($input['nome'])) ? $input['nome'] : null;
            $cpf = (!empty($input['cpf'])) ? $input['cpf'] : null;
            $modalidade = (!empty($input['modalidade'])) ? $input['modalidade'] : null;
            $valorfinanciado = (!empty($input['valorfinanciado'])) ? $input['valorfinanciado'] : null;
            $situacao = (!empty($input['situacao'])) ? $input['situacao'] : null;
            $observacao = (!empty($input['observacao'])) ? $input['observacao'] : null;
            $cca = $usuario['cca'];
            $assinado = (!empty($input['assinado'])) ? $input['assinado'] : null;

            $id = filter_var($id);
            $nome = filter_var($nome);
            $cpf = filter_var($cpf);
            $modalidade = filter_var($modalidade);
            $valorfinanciado = filter_var($valorfinanciado);
            $situacao = filter_var($situacao);
            $observacao = filter_var($observacao);
            $cca = filter_var($cca);
            $assinado = filter_var($assinado);

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