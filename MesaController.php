<?php 

require_once './models/Mesa.php';

class MesaController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoMesa = $parametros['codigoMesa'];
        $estadoMesa = $parametros['estadoMesa'];      

        // Creamos el Mesa
        $mesa = new Mesa();
        $mesa->codigoMesa = $codigoMesa;
        $mesa->estadoMesa = $estadoMesa;

        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}





?>