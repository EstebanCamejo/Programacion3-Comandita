<?php 

require_once './models/Mesa.php';

class MesaController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoMesa = $parametros['codigoMesa'];
       

        // Creamos el Mesa
        $mesa = new Mesa();
        $mesa->codigoMesa = $codigoMesa;
        $mesa->estadoMesa = 4;
        $mesa->fechaAlta = date ('Y-m-d H:i:s');
        $mesa->fechaModificacion = date (':iY-m-d H:s');//"0000-00-00";
        $mesa->fechaBaja = date (':iY-m-d H:s');//"0000-00-00";
        $mesa->activo = 1;


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

    public function CerrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoMesa = $parametros["codigoMesa"];
        Mesa::cerrarMesa($codigoMesa);

        $payload = json_encode(array("mensaje" => "Mesa cerrada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigoMesa = $args["codigoMesa"];
        Mesa::borrarMesa($codigoMesa);

        $payload = json_encode(array("mensaje" => "Mesa dada de baja cerrada con exito"));

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


    public function MasUsada($request, $response, $args)
    {
        // Buscamos mesa mas usada y retornamos el codigo
        
        $pedido = new Pedido(); 
        
        $mesaMasUsadaMensaje = $pedido->mesaMasUsada();
        
        $payload = json_encode(array("mensaje" => "$mesaMasUsadaMensaje"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
}





?>