<?php 

require_once './models/PedidoProducto.php';
require_once './models/Producto.php';


class PedidoProductoController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $codigoPedido = $parametros['codigoPedido'];
        $idProducto = $parametros['idProducto'];       
        $codigoMesa = $parametros['codigoMesa']; 
        
        
        $producto = Producto::obtenerProducto($idProducto);

        // Creamos el pedido producto
        $pedidoproducto = new PedidoProducto();
        $pedidoproducto->codigoPedido = $codigoPedido;
        $pedidoproducto->idProducto = $idProducto;      
        $pedidoproducto->codigoMesa = $codigoMesa;
        $pedidoproducto->fechaAlta =  date ('Y-m-d H:i:s');
        $pedidoproducto->fechaModificacion =  date (':iY-m-d H:s');//"0000-00-00";
        $pedidoproducto->fechaBaja =  date (':iY-m-d H:s');//"0000-00-00";
        $pedidoproducto->estado = 1; 
        $pedidoproducto->activo = 1;       
        $pedidoproducto->tiempoEstimado =  $producto->tiempoPreparacion;
        $pedidoproducto->sector = $producto->sector;
        $pedidoproducto->nombre = $producto->nombre;


        $pedidoproducto->crearPedidoProducto();
        

        $payload = json_encode(array("mensaje" => "Pedido Producto creado con exito",
        "codigoPedido"=>$pedidoproducto->codigoPedido,          
        "codigoMesa"=>$pedidoproducto->codigoMesa,
        "fechaAlta"=>$pedidoproducto->fechaAlta,
        "tiempoEstimado"=>$pedidoproducto->tiempoEstimado,
        "sector"=>$pedidoproducto->sector,
        "nombre"=>$pedidoproducto->nombre,
        "precio"=>$producto->precio
        ),JSON_PRETTY_PRINT);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = PedidoProducto::obtenerTodos();
        if(!empty($lista))
        {
          $payload = json_encode(array("lista de Pedidos Productos" => $lista),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("No se encontraron pedidos productos"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por id
        $id = $args['id'];
        $pedidoproducto = PedidoProducto::obtenerPedidoProducto($id);
        if(!empty($pedidoproducto))
        {
          $payload = json_encode(array("pedido producto encontrado" =>$pedidoproducto),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" =>"Pedido producto no encontrado"),JSON_PRETTY_PRINT);
        }
      
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPendientes($request, $response, $args)
    {
        // Buscamos producto por id
        $sector = $args['sector'];
        $pedidoproducto = PedidoProducto::obtenerPedidosPorSector($sector);
        if(!empty($pedidoproducto))
        {
          $payload = json_encode(array("listado pedido producto pendientes"=>$pedidoproducto),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" =>"No se encontraron pendientes"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigoPedido = $args["id"];
        //var_dump($codigoPedido);
        $pedidoProductoborrado = PedidoProducto::borrarPedidoProducto($codigoPedido);
        if($pedidoProductoborrado)
        {
          $payload = json_encode(array("mensaje" => "Pedido Producto borrado con exito"),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "Error, el pedido producto no se borro"),JSON_PRETTY_PRINT);
        }       

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $estado = $parametros['estado'];

        // Creamos el pedido
        $pedidoProducto = new PedidoProducto();
        $pedidoProducto->id = $id;
        $pedidoProducto->tiempoEstimado = $tiempoEstimado;
        $pedidoProducto->estado = $estado;

        if($estado == 2)
        {
          $estadoModificado = "en proceso";
        }else if($estado == 3)
        {
          $estadoModificado = "listo para servir";
        }          
        else
        {
          $estadoModificado = $pedidoProducto->estado;
        }

        $ppNombre = PedidoProducto::obtenerNombrePedidoProductoPorId($id);

        $modificado = PedidoProducto::modificarPedidoProducto($pedidoProducto);  
        
        if($modificado)
        {
          $payload = json_encode(array("mensaje" => "Pedido Producto modificado con exito",
          "id"=>$pedidoProducto->id,      
          "tiempoEstimado"=>$pedidoProducto->tiempoEstimado,
          "estado"=>$estadoModificado,
          "nombre"=>$ppNombre),JSON_PRETTY_PRINT);                
        
        }else 
        {
          $payload = json_encode(array("mensaje" => "Error, al intentar modificacion"),JSON_PRETTY_PRINT);
        }
      
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
}


?>