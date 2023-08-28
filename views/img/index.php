<?php    
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY,Origin,X-Requested-With, Content-Type, Accept,Access-Control-Request-Method,Access-Request-Headers,Authorization");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('content-type: application/json; charset=utf-8');
    header('HTTP/1.1 200 OK');


$json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR

$params = json_decode($json); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE


$nombreArchivo = $params->nombreArchivo;
$archivo = $params->base64textString;
$archivo = base64_decode($archivo);

$filePath = $_SERVER['DOCUMENT_ROOT']."/views/img/".$nombreArchivo;
file_put_contents($filePath, $archivo);


class Result {

    public $resultado;
    public $mensaje;

}
// GENERA LOS DATOS DE RESPUESTA
$response = new Result();

$response->resultado = 'OK';
$response->mensaje = 'SE SUBIO EXITOSAMENTE';

header('Content-Type: application/json');
echo json_encode($response); // MUESTRA EL JSON GENERADO */