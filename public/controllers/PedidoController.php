<?php 

require_once './models/Pedido.php';

class PedidoController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        //$codigoPedido = $parametros['codigoPedido'];
        $idCliente = $parametros['idCliente'];
        $codigoMesa = $parametros['codigoMesa'];
        $idEmpleado = $parametros['idEmpleado'];      
       // $precioFinal = $parametros['precioFinal'];
       // $estadoPedido = $parametros ['estadoPedido'];

     /* if(isset($parametros['foto']) && $parametros['foto'] != null)
       {
           $fotoMesa = $parametros['foto'];
       }
       else
       {
           $fotoMesa = "No hay imagen.";
       }*/
       $fotoMesa = "No hay imagen.";
        // Creamos el Pedido
        $pedido = new Pedido();
       // $pedido->codigoPedido = $codigoPedido;
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

       

        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito ".$pedido->codigoPedido));

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

    
    public function CancelarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoPedido = $parametros["codigoPedido"];
        Pedido::cancelarPedido($codigoPedido);

        $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigoPedido = $args["codigoPedido"];
        Pedido::borrarPedido($codigoPedido);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

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
       // $fechaFinalizacion = $parametros['fechaFinalizacion'];
        $estadoPedido = $parametros['estadoPedido'];
        $precioFinal = $parametros['precioFinal'];

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigoPedido = $codigoPedido;
        $pedido->idCliente = $idCliente;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->idEmpleado = $idEmpleado;
      //  $pedido->fechaFinalizacion = $fechaFinalizacion;
        $pedido->estadoPedido = $estadoPedido;
        if($pedido->estadoPedido == 2)
        {
          $pedido->fechaFinalizacion = date ('Y-m-d H:i:s'); 
        }else
        {
          $pedido->fechaFinalizacion =  date (':iY-m-d H:s');//"0000-00-00";
        }


        $pedido->precioFinal = $precioFinal;

        Pedido::modificarPedido($pedido);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function TraerDemoraDeUno($request, $response, $args)
    {
        // Buscamos mesa por codigo
    
        $codigoPedido = $args['codigoPedido'];            
        $mensaje = "El tiempo de espera es de ";
        $pedido = Pedido::calcularDemoraPedido($codigoPedido);
        $payload = json_encode($mensaje.$pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function SubirFoto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoPedido = $parametros['codigoPedido'];       
        $idPedido = Pedido::ValidarPedidoPorCodigo($codigoPedido);

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigoPedido = $codigoPedido;
        $pedido->id= $idPedido;

        $pedido->GuardarImagen( "Fotos/{$codigoPedido}", $_FILES['foto']);

        if($pedido->SubirFoto() > 0)
        {
            $payload = json_encode(array("mensaje" => "Foto Agregada."));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se pudo agregar la imagen."));
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
          $payload = json_encode(array("mensaje" => "Mesas actualizadas.", "mesasModificadas" => $cambiosRealizados));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay mesas que actualizar."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }



}





?>