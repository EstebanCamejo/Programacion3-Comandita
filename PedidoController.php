<?php 

require_once './models/Pedido.php';

class PedidoController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $codigoPedido = $parametros['codigoPedido'];
        $idCliente = $parametros['idCliente'];
        $codigoMesa = $parametros['codigoMesa'];
        $idEmpleado = $parametros['idEmpleado'];      
        $precioFinal = $parametros['precioFinal'];
        //$fechaFinalizacion =$parametros ['fechaFinalizacion'];
        $estadoPedido = $parametros ['estadoPedido'];

        // Creamos el Pedido
        $pedido = new Pedido();
        $pedido->codigoPedido = $codigoPedido;
        $pedido->idCliente = $idCliente;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->idEmpleado = $idEmpleado;
        $pedido->fechaPreparacion =  date('Y-m-d H:i:s');
        $pedido->fechaFinalizacion =  date (':iY-m-d H:s');
        $pedido->precioFinal = $precioFinal;
        $pedido->estadoPedido = $estadoPedido;

        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por codigo
        $codigo = $args['codigoPedido'];
        $pedido = Pedido::obtenerPedido($codigo);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
    public function BorrarUno($request, $response, $args)
    {
        $codigoPedido = $args["codigoPedido"];
        Pedido::cancelarPedido($codigoPedido);

        $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoPedido = $parametros['codigoPedido'];
        $idCliente = $parametros['idCliente'];
        $codigoMesa = $parametros['codigoMesa'];
        $idEmpleado = $parametros['idEmpleado'];
        $fechaPreparacion = $parametros['fechaPreparacion'];
        $fechaFinalizacion = $parametros['fechaFinalizacion'];
        $estadoPedido = $parametros['estadoPedido'];
        $precioFinal = $parametros['precioFinal'];

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigoPedido = $codigoPedido;
        $pedido->idCliente = $idCliente;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->idEmpleado = $idEmpleado;
        $pedido->fechaPreparacion = $fechaPreparacion;
        $pedido->fechaFinalizacion = $fechaFinalizacion;
        $pedido->estadoPedido = $estadoPedido;
        $pedido->precioFinal = $precioFinal;

        Pedido::modificarPedido($pedido);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}





?>