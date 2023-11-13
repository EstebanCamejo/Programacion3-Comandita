<?php 

require_once './models/PedidoProducto.php';

class PedidoProductoController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $codigoPedido = $parametros['codigoPedido'];
        $idProducto = $parametros['idProducto'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $codigoMesa = $parametros['codigoMesa'];       
        // Creamos el pedido producto
        $pedidoproducto = new PedidoProducto();
        $pedidoproducto->codigoPedido = $codigoPedido;
        $pedidoproducto->idProducto = $idProducto;
        $pedidoproducto->tiempoEstimado = $tiempoEstimado;
        $pedidoproducto->codigoMesa = $codigoMesa;

        $pedidoproducto->crearPedidoProducto();

        $payload = json_encode(array("mensaje" => "Pedido Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = PedidoProducto::obtenerTodos();
        $payload = json_encode(array("listaPedidoProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por id
        $id = $args['id'];
        $pedidoproducto = PedidoProducto::obtenerPedidoProducto($id);
        $payload = json_encode($pedidoproducto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $pedidoProductoId = $args["id"];
        PedidoProducto::borrarPedidoProducto($pedidoProductoId);

        $payload = json_encode(array("mensaje" => "Pedido Producto borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}


?>