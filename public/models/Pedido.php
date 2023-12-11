<?php 

class Pedido
{
    public $id;
    public $codigoPedido; //ok
    public $idCliente;//ok
    public $codigoMesa; //ok
    public $idEmpleado;//ok
  //  public $fechaPreparacion;
    public $fechaFinalizacion;    
    public $estadoPedido;
    public $fechaAlta;
    public $fechaModificacion;
    public $fechaBaja;
    public $precioFinal;
    public $activo;
    public $foto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
         // Genera un código alfanumérico de 5 caracteres
        $codigoAlfanumerico = Pedido::generarCodigoAlfanumerico(5);
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (codigoPedido, idCliente, codigoMesa, 
        idEmpleado, fechaFinalizacion, estadoPedido, 
        fechaAlta, fechaModificacion, fechaBaja, precioFinal, activo, foto) 
        VALUES (:codigoPedido, :idCliente, :codigoMesa, :idEmpleado, :fechaFinalizacion, 
        :estadoPedido, :fechaAlta, :fechaModificacion, :fechaBaja, :precioFinal, :activo, :foto)");   

        $consulta->bindValue(':codigoPedido', $this->codigoPedido = $codigoAlfanumerico);
        $consulta->bindValue(':idCliente', $this->idCliente, PDO::PARAM_INT);    
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);   
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);          
        $consulta->bindValue(':fechaFinalizacion', $this->fechaFinalizacion, PDO::PARAM_STR);   
        $consulta->bindValue(':estadoPedido', $this->estadoPedido, PDO::PARAM_INT); 
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', $this->fechaModificacion, PDO::PARAM_STR);
        $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);           
        $consulta->bindValue(':precioFinal', $this->precioFinal, PDO::PARAM_INT); 
        $consulta->bindValue(':activo', $this->activo, PDO::PARAM_INT);    
        $consulta->bindValue(':foto', $this->activo, PDO::PARAM_STR);    
        //$precioFinal = $this->calcularPrecioFinal($this->codigoPedido);
        //$consulta->bindValue(':precioFinal', $precioFinal, PDO::PARAM_INT); 
                
        $consulta->execute();
        
        return $objAccesoDatos->obtenerUltimoId();
    }

    function generarCodigoAlfanumerico($length) {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        $caracteresLength = strlen($caracteres);
    
        for ($i = 0; $i < $length; $i++) {
            $codigo .= $caracteres[rand(0, $caracteresLength - 1)];
        };    
        return $codigo;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();      
       // $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido");
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedido.id, 
        pedido.codigoPedido, 
        pedido.idCliente, 
        pedido.codigoMesa, 
        pedido.idEmpleado,
        pedido.fechaFinalizacion, 
        estadopedido.estado as estadoPedido, 
        pedido.fechaAlta,
        pedido.fechaModificacion,
        pedido.fechaBaja,
        pedido.precioFinal,
        pedido.activo,
        pedido.foto
        FROM 
            pedido 
        LEFT JOIN 
            estadopedido ON pedido.estadoPedido = estadopedido.id WHERE pedido.activo = 1");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedido.id, 
            pedido.codigoPedido, 
            pedido.idCliente, 
            pedido.codigoMesa, 
            pedido.idEmpleado, 
            pedido.fechaFinalizacion, 
            estadopedido.estado as estadoPedido, 
            pedido.fechaAlta,
            pedido.fechaModificacion,
            pedido.fechaBaja,
            pedido.precioFinal, 
            pedido.activo,
            pedido.foto
            FROM pedido 
            JOIN estadopedido ON pedido.estadoPedido = estadopedido.id 
            WHERE pedido.codigoPedido = :codigoPedido AND pedido.activo = 1");
        $consulta->bindValue(':codigoPedido', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
    
    public static function cancelarPedido($codigoPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 3;
        $fechaModificacion = date('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido 
        SET estadoPedido = :estadoPedido, fechaModificacion = :fechaModificacion
        WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estadoPedido', $nuevoEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->rowCount() > 0;
    }

    public static function borrarPedido($codigoPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $activo = 0;
        $fechaBaja = date('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido 
        SET activo = :activo, fechaBaja = :fechaBaja
        WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $activo, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fechaBaja, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->rowCount() > 0;
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $fechaModificacion = date('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido 
        SET idCliente = :idCliente, 
        codigoMesa = :codigoMesa,
        idEmpleado = :idEmpleado,
        fechaFinalizacion = :fechaFinalizacion,
        estadoPedido = :estadoPedido,
        fechaModificacion = :fechaModificacion,
        precioFinal = :precioFinal
        WHERE codigoPedido = :codigoPedido");

        $consulta->bindValue(':idCliente', $pedido->idCliente, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $pedido->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $pedido->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaFinalizacion', $pedido->fechaFinalizacion, PDO::PARAM_STR);
        $consulta->bindValue(':estadoPedido', $pedido->estadoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':precioFinal', $pedido->precioFinal, PDO::PARAM_INT);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR );

        $consulta->bindValue(':codigoPedido', $pedido->codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount() > 0;
    }

    public static function calcularDemoraPedido($codigoPedido)
    {       
        $codigoMesa = Mesa::obtenerCodigoMesaPorCodigoPedido($codigoPedido);       
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
            $mensajeRetorno = json_encode($mensaje .$diferenciaTiempoFormateada , JSON_PRETTY_PRINT);
          }else
          {
            $mensajeRetorno = json_encode("Tiempo de espera vencido", JSON_PRETTY_PRINT);
          }

        } else {
            $mensajeRetorno = json_encode("ERROR en el cálculo del tiempo máximo.", JSON_PRETTY_PRINT);
        }
        
        return $mensajeRetorno;        
    }
    

    public static function obtenerPedidosVencidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigoPedido FROM pedido WHERE activo = 1");
    
        $consulta->execute();
        $codigosPedidos = $consulta->fetchAll(PDO::FETCH_COLUMN);
    
        $pedidosVencidos = [];
    
        foreach ($codigosPedidos as $codigoPedido) {
            $tiempoEspera = self::calcularDemoraPedido($codigoPedido);
            if ($tiempoEspera === json_encode("Tiempo de espera vencido", JSON_PRETTY_PRINT)) {
                $pedidosVencidos[] = $codigoPedido;
            }
        }
    
        return $pedidosVencidos;
    }
    

    public function GuardarImagen($ruta, $imagen)
    {
        $destino = $ruta . ".jpg";

        $this->foto_mesa = $destino;
        
        if(!move_uploaded_file($imagen["tmp_name"], $destino))
        {
            $this->foto_mesa = "Error";
        }

    }


    public function SubirFoto()
    {
        $accesoDb = AccesoDatos::obtenerInstancia();

        $consulta = $accesoDb->prepararConsulta("UPDATE pedido SET foto = :foto WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->codigoPedido, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->rowCount();

    }
    
    public static function ValidarPedidoPorCodigo($codigoPedido)
    {
        $listaPedido = Pedido::obtenerTodos();
        $esValido = -1;      

        foreach ($listaPedido as $pedido) 
        {              
            if($pedido->codigoPedido == $codigoPedido)
            {
                $esValido = $pedido->id;
                break;
            }
        }
        return $esValido;
    }
    
    
    public static function ListosParaServir()
    {        
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $cambiosRealizados = false;
        $mesasModificadas = [];
        // Consulta para obtener los códigos de mesa de pedidos con estado igual a 2 == en preparacion
        $consultaPedidos = $objAccesoDato->prepararConsulta(
            "SELECT codigoMesa FROM pedido WHERE estadoPedido = 2"
        );
        $consultaPedidos->execute();
        $pedidos = $consultaPedidos->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pedidos as $pedido) {
            $codigoMesa = $pedido['codigoMesa'];

            // Actualizar el estadoMesa a 2 para todas las filas en la tabla mesa 
            // con el mismo código de mesa
            $consultaMesa = $objAccesoDato->prepararConsulta(
                "UPDATE mesa SET estadoMesa = 2 WHERE codigoMesa = :codigoMesa"
            );
            $consultaMesa->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
            $consultaMesa->execute();
            $cambiosRealizados = true;
            $mesasModificadas [] = $codigoMesa;
        }
        return ($cambiosRealizados) ? $mesasModificadas : false;     
    }

    public static function cobrarCuenta($codigoMesa) 
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        // Paso 1: Buscar y acumular precioFinal
        $consulta = $objAccesoDato->prepararConsulta("SELECT SUM(precioFinal) AS total 
        FROM pedido WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();
        $totalPrecioFinal = $consulta->fetch(PDO::FETCH_ASSOC)['total'];

        // Paso 2: Cambiar estadoMesa a 3
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estadoMesa = 3 
        WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $totalPrecioFinal;
    }

    public function mesaMasUsada() {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        // Paso 1: Contar las veces que aparece cada codigoMesa en la tabla pedido
        $consulta = $objAccesoDato->prepararConsulta("SELECT codigoMesa, COUNT(*) AS total 
        FROM pedido GROUP BY codigoMesa ORDER BY total DESC LIMIT 1");
        $consulta->execute();
        $mesaMasUsada = $consulta->fetch(PDO::FETCH_ASSOC);

        // Paso 2: Retornar el codigoMesa más utilizado y la cantidad de veces que aparece
        if ($mesaMasUsada) {
            return "Código de Mesa: {$mesaMasUsada['codigoMesa']}, Aparece {$mesaMasUsada['total']} veces.";
        } else {
            return "No hay datos en la tabla pedido.";
        }
    }

    public static function obtenerHoraPedido($codigoPedido, $codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT DATE_FORMAT(fechaAlta, '%H:%i:%s') as horaAlta 
            FROM pedido 
            WHERE codigoPedido = :codigoPedido AND codigoMesa = :codigoMesa");

        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);

        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado['horaAlta'];
        } else {
            return null; // O podrías manejar el caso en que no se encuentre el pedido.
        }
    }

    

}


?>