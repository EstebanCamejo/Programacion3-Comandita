<?php 

require_once './models/Cliente.php';

class ClienteController 
{
    
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];                
      $cliente = new Cliente();
      $cliente->nombre = $nombre;        
      $cliente->crearCliente();

      if($cliente)
      {
        $payload = json_encode(array("mensaje" => "Cliente creado con exito", JSON_PRETTY_PRINT));
      }else
      {
        $payload = json_encode(array("mensaje" => "No se pudo crear el cliente", JSON_PRETTY_PRINT));
      }
    
      $response->getBody()->write($payload);
      return $response
      ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerPedido($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoPedido = $parametros['codigoPedido'];
      $codigoMesa = $parametros['codigoMesa'];        

      $mensaje = "El tiempo de espera es de ";
      $tiempoDeEspera = Cliente::VerTiempoEstimadoMaximo($codigoPedido,$codigoMesa);
      $horaDelPedido = Pedido::obtenerHoraPedido($codigoPedido,$codigoMesa);        
      $horarioActual =  date('H:i:s');

        if ($horaDelPedido && $tiempoDeEspera) {

          $tiempoDeEsperaFormateado = ClienteController::horaASegundos($tiempoDeEspera);
          $horaDelPedidoFormateado = ClienteController::horaASegundos($horaDelPedido);
          $horarioActualFormateado = ClienteController::horaASegundos($horarioActual);

          $horaActualMasTiempoEspera = $horaDelPedidoFormateado + $tiempoDeEsperaFormateado;
          $diferenciaTiempo = $horaActualMasTiempoEspera - $horarioActualFormateado;

          $diferenciaTiempoFormateada = ClienteController::segundosAHora($diferenciaTiempo);

          if($diferenciaTiempoFormateada>0)
          {
            $payload = json_encode($mensaje .$diferenciaTiempoFormateada , JSON_PRETTY_PRINT);
          }else
          {
            $payload = json_encode("Tiempo de espera vencido", JSON_PRETTY_PRINT);
          }

        } else {
            $payload = json_encode("ERROR en el cálculo del tiempo máximo.", JSON_PRETTY_PRINT);
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function horaASegundos($hora) 
    {
      list($horas, $minutos, $segundos) = explode(':', $hora);
      return $horas * 3600 + $minutos * 60 + $segundos;
    }
    
    public static function segundosAHora($segundos) 
    {  
      $horas = floor($segundos / 3600);
      $segundos %= 3600;
      $minutos = floor($segundos / 60);
      $segundos %= 60;
      return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
    }
} 
?>