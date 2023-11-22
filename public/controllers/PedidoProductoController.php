<?php 

require_once './models/PedidoProducto.php';

class PedidoProductoController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $codigoPedido = $parametros['codigoPedido'];
        $idProducto = $parametros['idProducto'];       
        $codigoMesa = $parametros['codigoMesa'];       
        // Creamos el pedido producto
        $pedidoproducto = new PedidoProducto();
        $pedidoproducto->codigoPedido = $codigoPedido;
        $pedidoproducto->idProducto = $idProducto;
        $pedidoproducto->tiempoEstimado = (':iY-m-d H:s');//"0000-00-00";
        $pedidoproducto->codigoMesa = $codigoMesa;
        $pedidoproducto->fechaAlta =  date ('Y-m-d H:i:s');
        $pedidoproducto->fechaModificacion =  date (':iY-m-d H:s');//"0000-00-00";
        $pedidoproducto->fechaBaja =  date (':iY-m-d H:s');//"0000-00-00";
        $pedidoproducto->estado = 1; 
        $pedidoproducto->activo = 1;

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

    public function TraerPendientes($request, $response, $args)
    {
        // Buscamos producto por id
        $sector = $args['sector'];
        $pedidoproducto = PedidoProducto::obtenerPedidosPorSector($sector);
        $payload = json_encode($pedidoproducto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigoPedido = $args["codigoPedido"];
        var_dump($codigoPedido);
        PedidoProducto::borrarPedidoProducto($codigoPedido);

        $payload = json_encode(array("mensaje" => "Pedido Producto borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $codigoPedido = $parametros['codigoPedido'];
        $idProducto = $parametros['idProducto'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $codigoMesa = $parametros['codigoMesa'];
        $estado = $parametros['estado'];

        // Creamos el pedido
        $pedidoProducto = new PedidoProducto();
        $pedidoProducto->id = $id;
        $pedidoProducto->codigoPedido = $codigoPedido;
        $pedidoProducto->idProducto = $idProducto;
        $pedidoProducto->tiempoEstimado = $tiempoEstimado;
        $pedidoProducto->codigoMesa = $codigoMesa;
        $pedidoProducto->estado = $estado;
        PedidoProducto::modificarPedidoProducto($pedidoProducto);
        $pedidoProducto->estado = 1;

        $payload = json_encode(array("mensaje" => "Pedido Producto modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
}


?>