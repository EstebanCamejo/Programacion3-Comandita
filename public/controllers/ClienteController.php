<?php 

require_once './models/Cliente.php';

class ClienteController 
{
    
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];        
        // Creamos el cliente
    
        $cliente = new Cliente();
        $cliente->nombre = $nombre;        
        $cliente->crearCliente();

        $payload = json_encode(array("mensaje" => "Cliente creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerPedido($request, $response, $args)
    {
        // Buscamos mesa por codigo
        $parametros = $request->getParsedBody();

        $codigoPedido = $parametros['codigoPedido'];
        $codigoMesa = $parametros['codigoMesa'];        

        $mensaje = "El tiempo de espera es de ";
        $tiempoDeEspera = Cliente::VerTiempoEstimadoMaximo($codigoPedido,$codigoMesa);
        $payload = json_encode($mensaje.$tiempoDeEspera);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
} 
?>