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

        // Creamos el Pedido
        $pedido = new Pedido();
        $pedido->codigoPedido = $codigoPedido;
        $pedido->idCliente = $idCliente;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->idEmpleado = $idEmpleado;
        $pedido->fechaPreparacion = date ('Y-m-d H:i:s');
        $pedido->precioFinal = $precioFinal;

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
}





?>