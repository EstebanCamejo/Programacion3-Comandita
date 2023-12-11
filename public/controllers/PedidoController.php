<?php 

require_once './models/Pedido.php';

class PedidoController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $idCliente = $parametros['idCliente'];
        $codigoMesa = $parametros['codigoMesa'];
        $idEmpleado = $parametros['idEmpleado'];      

        $fotoMesa = "No hay imagen.";
       
        $pedido = new Pedido();       
        $pedido->idCliente = $idCliente;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->idEmpleado = $idEmpleado;
        $pedido->fechaFinalizacion =  date (':iY-m-d H:s');//"0000-00-00";
        $pedido->precioFinal = 0;
        $pedido->estadoPedido = 1;
        $pedido->fechaAlta =  date ('Y-m-d H:i:s');
        $pedido->fechaModificacion =  date (':iY-m-d H:s');//"0000-00-00";
        $pedido->fechaBaja =  date (':iY-m-d H:s');//"0000-00-00";
        $pedido->activo = 1;
        $pedido->foto = $fotoMesa;
       
        $pedidoCreado = $pedido->crearPedido();
        if($pedidoCreado)
        {
          $payload = json_encode(array("mensaje" => "Pedido creado con exito ",
          "codigoPedido"=>$pedido->codigoPedido,
          "fechaAlta"=>$pedido->fechaAlta),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "El pedido no pudo crearse"),JSON_PRETTY_PRINT);
        }       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();

        if(!empty($lista))
        {
          $payload = json_encode(array("lista de pedidos" => $lista),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("Error al traer la lista de pedidos"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por codigo
        $codigo = $args['codigoPedido'];
        $pedido = Pedido::obtenerPedido($codigo);
        if(!empty($pedido))
        {
          $payload = json_encode(array("Datos del pedido solicitado"=>$pedido),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("El pedido solicitado no se encontro"),JSON_PRETTY_PRINT);
        }        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
    public function CancelarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoPedido = $parametros["codigoPedido"];
        $pedidoCancelado = Pedido::cancelarPedido($codigoPedido);

        if($pedidoCancelado)
        {
          $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "Error al cancelar el pedido"),JSON_PRETTY_PRINT);
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigoPedido = $args["codigoPedido"];
        $pedidoBorrado = Pedido::borrarPedido($codigoPedido);

        if($pedidoBorrado)
        {
          $payload = json_encode(array("mensaje" => "Pedido borrado con exito",
          "codigo del pedido"=>$codigoPedido),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "Error al borrar el pedido"),JSON_PRETTY_PRINT);
        }

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
        $estadoPedido = $parametros['estadoPedido'];
        $precioFinal = $parametros['precioFinal'];

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigoPedido = $codigoPedido;
        $pedido->idCliente = $idCliente;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->idEmpleado = $idEmpleado;
        $pedido->estadoPedido = $estadoPedido;
        if($pedido->estadoPedido == 2)
        {
          $pedido->fechaFinalizacion = date ('Y-m-d H:i:s'); 
        }else
        {
          $pedido->fechaFinalizacion =  date (':iY-m-d H:s');//"0000-00-00";
        }
        $pedido->precioFinal = $precioFinal;

        $pedidoModificado = Pedido::modificarPedido($pedido);
        if($pedidoModificado)
        {
          $payload = json_encode(array("mensaje" => "Pedido modificado con exito",
          "codigoPedido"=>$pedido->codigoPedido ,
          "idCliente"=>$pedido->idCliente ,
          "codigoMesa"=>$pedido->codigoMesa ,
          "idEmpleado"=>$pedido->idEmpleado ,
          "estadoPedido"=>$pedido->estadoPedido),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "Error, el pedido no se modifico"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerDemoraDeUno($request, $response, $args)
    {
        $codigoPedido = $args['codigoPedido'];            
       
        $demoraPedido = Pedido::calcularDemoraPedido($codigoPedido);
        if($demoraPedido)
        {
          $mensaje = $demoraPedido;         
        }else
        {
          $mensaje = array("Error al calcular rl tiempo de espera"); 
        }

        $payload = json_encode($mensaje,JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPedidosVencidos($request, $response, $args)
    {
        $listavencidos = Pedido::obtenerPedidosVencidos();

        $pedidosDetalles = [];

        foreach ($listavencidos as $codigoPedido) {
            $detallePedido = Pedido::obtenerPedido($codigoPedido);
            if ($detallePedido) {
                $pedidosDetalles[] = $detallePedido;
            }
        }
        if (!empty($pedidosDetalles)) {
            $payload = json_encode(array("lista de pedidos vencidos" => $pedidosDetalles), JSON_PRETTY_PRINT);
        } else {
            $payload = json_encode(array("No existen detalles de pedidos vencidos al momento"), JSON_PRETTY_PRINT);
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function SubirFoto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoPedido = $parametros['codigoPedido'];  
        // Creamos el pedido
        $pedido = new Pedido();

        $idPedido = Pedido::ValidarPedidoPorCodigo($codigoPedido);

        $pedido->codigoPedido = $codigoPedido;
        $pedido->id= $idPedido;

        $pedido->GuardarImagen( "Fotos/{$codigoPedido}", $_FILES['foto']);

        if($pedido->SubirFoto() > 0)
        {
            $payload = json_encode(array("mensaje" => "Foto Agregada.","nombre foto"=>$codigoPedido),JSON_PRETTY_PRINT);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se pudo agregar la imagen."),JSON_PRETTY_PRINT);
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListosParaServir($request, $response, $args)
    {
        // Buscamos mesa por codigo
        $pedido = new Pedido();

        $cambiosRealizados = $pedido->ListosParaServir();
        if(is_array($cambiosRealizados))
        {
          $payload = json_encode(array("mensaje" => "Mesas actualizadas.",
           "mesasModificadas" => $cambiosRealizados, "estadoMesa" =>"comiendo"),JSON_PRETTY_PRINT);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay mesas que actualizar."),JSON_PRETTY_PRINT);
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function CobrarCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoMesa = $parametros['codigoMesa'];
   
        $totalPrecioFinal =  Pedido::cobrarCuenta($codigoMesa);
        if ($totalPrecioFinal !== false) 
        {
          //cambiar estado de mesa a 3
          $payload = json_encode(array("mensaje" => "El estado de la mesa paso a pagando",
          "totalPrecioFinal: "=>$totalPrecioFinal),JSON_PRETTY_PRINT);
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se pudo cobrar la mesa.", 
          "codigoMesa:" =>$codigoMesa),JSON_PRETTY_PRINT);
        }       

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}





?>