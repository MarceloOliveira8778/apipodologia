<?php

//Permitindo que a API seja acessada de qualquer domínio
header("Access-Control-Allow-Origin: *");

//Permitindo outros sites fazerem tudo (GET, POST, PUT e DELETE)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

//Avisando os clients o tipo de retorno
header("Content-Type: application/json");

//Retornando o JSON
echo json_encode($array);
exit;

?>