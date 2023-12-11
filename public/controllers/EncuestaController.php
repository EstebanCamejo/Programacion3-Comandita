<?php 
require_once './models/Encuesta.php';
class EncuestaController
{
    public function Encuesta($request, $response, $args)
    {
        // Buscamos mesa por codigo
        $parametros = $request->getParsedBody();
       
        $puntuacionMozo = $parametros['puntuacionMozo'];
        $puntuacionCocina = $parametros['puntuacionCocina'];    
        $puntuacionMesa = $parametros['puntuacionMesa'];
        $puntuacionBebidas = $parametros['puntuacionBebidas']; 
        $comentario = $parametros['comentario'];
        $codigoMesa = $parametros['codigoMesa']; 
        $codigoPedido = $parametros['codigoPedido'];
        
    
        $encuesta = new Encuesta();
        $encuesta->puntuacionMozo = $puntuacionMozo;  
        $encuesta->puntuacionCocina = $puntuacionCocina;  
        $encuesta->puntuacionMesa = $puntuacionMesa;  
        $encuesta->puntuacionBebidas = $puntuacionBebidas;  
        $encuesta->comentario = $comentario;  
        $encuesta->codigoMesa = $codigoMesa;  
        $encuesta->codigoPedido = $codigoPedido;  
        $encuesta->fechaAlta = date ('Y-m-d H:i:s'); 
        $encuesta->activo = 1;  
    
        $encuesta->crearEncuesta();
        if($encuesta)
        {
          $payload = json_encode(array("mensaje" => "Encuesta creada con exito. Muchas gracias! ",
          "puntuacionMozo"=> $encuesta->puntuacionMozo ,  
          "puntuacionCocina"=>$encuesta->puntuacionCocina ,  
          "puntuacionMesa"=>$encuesta->puntuacionMesa ,  
          "puntuacionBebidas"=>$encuesta->puntuacionBebidas,  
          "comentario"=>$encuesta->comentario ,  
          "codigoMesa"=>$encuesta->codigoMesa ,  
          "codigoPedido"=>$encuesta->codigoPedido ,),JSON_PRETTY_PRINT);                      
        }else
        {
          $payload = json_encode(array("mensaje" => "Error al crear la encuesta."), JSON_PRETTY_PRINT);
        }
        
    
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejoresComentarios($request, $response, $args)
    {
        // Buscamos mejores promedios y mostramos los comentarios
        $encuesta = new Encuesta();
        $comentarios = $encuesta ->mejoresComentarios();      
        $payload = json_encode($comentarios,JSON_PRETTY_PRINT);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
} 





?>