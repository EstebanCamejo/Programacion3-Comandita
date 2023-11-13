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

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por codigo
        $codigo = $args['codigoMesa'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigoMesa = $args["codigoMesa"];
        Mesa::cerrarMesa($codigoMesa);

        $payload = json_encode(array("mensaje" => "Mesa cerrada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $codigoMesa = $parametros['codigoMesa'];
        $estadoMesa = $parametros['estadoMesa'];

        // Creamos la Mesa
        $mesa = new Mesa();
        $mesa->id = $id;
        $mesa->codigoMesa = $codigoMesa;
        $mesa->estadoMesa = $estadoMesa;        

        Mesa::modificarMesa($mesa);

        $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
}





?>