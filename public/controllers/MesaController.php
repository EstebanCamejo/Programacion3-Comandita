<?php 

require_once './models/Mesa.php';

class MesaController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

       // $codigoMesa = $parametros['codigoMesa'];       
        $mesa = new Mesa();
        //$mesa->codigoMesa = $codigoMesa;
        $mesa->estadoMesa = 4;
        $mesa->fechaAlta = date ('Y-m-d H:i:s');
        $mesa->fechaModificacion = date (':iY-m-d H:s');//"0000-00-00";
        $mesa->fechaBaja = date (':iY-m-d H:s');//"0000-00-00";
        $mesa->activo = 1;

        $mesa->crearMesa();
        if($mesa)
        {
          $payload = json_encode(array("mensaje" => "Mesa creada con exito",
          "codigoMesa"=>$mesa->codigoMesa,
          "fechaAlta"=>$mesa->fechaAlta,),JSON_PRETTY_PRINT);        
        }
        else
        {
          $payload = json_encode(array("mensaje" => "La mesa no se creo."),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
      
        if(empty($lista))
        {
          $error = array("error" => "No se encontraron mesas disponibles");
          $payload = json_encode($error, JSON_PRETTY_PRINT);

        }else
        {
          $payload = json_encode(array("lista Mesas" => $lista),JSON_PRETTY_PRINT);
        }      
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $codigo = $args['codigoMesa'];
        $mesa = Mesa::obtenerMesa($codigo);
        if(empty($mesa))
        {
          $error = array("error" => "No se encontraro la mesa buscada.");
          $payload = json_encode($error, JSON_PRETTY_PRINT);

        }else
        {
          $payload = json_encode(array("Mesa"=>$mesa),JSON_PRETTY_PRINT);
        }        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoMesa = $parametros["codigoMesa"];
        $mesaCerrada = Mesa::cerrarMesa($codigoMesa);

        if($mesaCerrada)
        {
          $payload = json_encode(array("mensaje" => "Mesa cerrada con exito","codigoMesa"=>$codigoMesa),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "No se pudo cerrar la mesa"),JSON_PRETTY_PRINT);
        }
       

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigoMesa = $args["codigoMesa"];
        $mesaBorrada = Mesa::borrarMesa($codigoMesa);

        if($mesaBorrada)
        {
          $payload = json_encode(array("mensaje" => "Mesa dada de baja cerrada con exito",
          "codigo mesa"=>$codigoMesa),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "No se pudo borrar la mesa"),JSON_PRETTY_PRINT);
        }
       
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

        $estadosPermitidos = [1, 2, 3, 4];
        if (!in_array($estadoMesa, $estadosPermitidos)) {
            $payload = json_encode(array("mensaje" => "El estado de la mesa no es válido"), JSON_PRETTY_PRINT);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Código de estado 400 para solicitud incorrecta
        }
        // Creamos la Mesa
        $mesa = new Mesa();
        $mesa->id = $id;
        $mesa->codigoMesa = $codigoMesa;
        $mesa->estadoMesa = $estadoMesa;        

        $estadosLegibles = [
          1 => "Esperando pedido",
          2 => "Comiendo",
          3 => "Pagando",
          4 => "Cerrada"
        ];
        $estadoLegible = $estadosLegibles[$estadoMesa];
        
        $mesaModificada = Mesa::modificarMesa($mesa);

        if($mesaModificada)
        {
          $payload = json_encode(array("mensaje" => "Mesa modificada con exito:",
          "codigoMesa"=>$mesa->codigoMesa,
          "estadoMesa"=>$estadoLegible),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "la mesa no se modifico"),JSON_PRETTY_PRINT);
        }
       

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function MasUsada($request, $response, $args)
    {
        // Buscamos mesa mas usada y retornamos el codigo
        
        $pedido = new Pedido(); 
        
        $mesaMasUsadaMensaje = $pedido->mesaMasUsada();
        
        $payload = json_encode(array("mesaMasUsadaMensaje:" => "$mesaMasUsadaMensaje"),JSON_PRETTY_PRINT);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    

    public function MasBarataAMasCara($request, $response, $args)
    {
        $lista = Mesa::facturacionAscendente();

        if (empty($lista)) {
            $error = array("error" => "No se encontraron mesas disponibles");
            $payload = json_encode($error, JSON_PRETTY_PRINT);
        } else {
            $payload = json_encode(array("Facturacion ascendente de mesas" => $lista), JSON_PRETTY_PRINT);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function facturacionEntreDosFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoMesa = $parametros["codigoMesa"];
        $fechaInicio = $parametros["fechaInicio"];
        $fechaFin = $parametros["fechaFin"];
    
        $totalFacturacion = Mesa::listadoFacturacionEntreFechas($codigoMesa, $fechaInicio, $fechaFin);
    
        if ($totalFacturacion !== null) {
            $payload = json_encode(array(
                "Total facturado entre $fechaInicio y $fechaFin para la mesa $codigoMesa" => $totalFacturacion
            ), JSON_PRETTY_PRINT);
        } else {
            $payload = json_encode(array(
                "mensaje" => "No hay facturación para estas fechas o para esta mesa."
            ), JSON_PRETTY_PRINT);
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    


    
}





?>