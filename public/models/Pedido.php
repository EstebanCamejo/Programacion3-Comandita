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

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
         // Genera un código alfanumérico de 5 caracteres
        $codigoAlfanumerico = Pedido::generarCodigoAlfanumerico(5);
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (codigoPedido, idCliente, codigoMesa, 
        idEmpleado, fechaFinalizacion, estadoPedido, 
        fechaAlta, fechaModificacion, fechaBaja, precioFinal, activo) 
        VALUES (:codigoPedido, :idCliente, :codigoMesa, :idEmpleado, :fechaFinalizacion, 
        :estadoPedido, :fechaAlta, :fechaModificacion, :fechaBaja, :precioFinal, :activo)");   

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
        pedido.fechaFinalizacion, 
        estadopedido.estado as estadoPedido, 
        pedido.fechaAlta,
        pedido.fechaModificacion,
        pedido.fechaBaja,
        pedido.precioFinal
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
            pedido.precioFinal 
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
    }

    public static function calcularDemoraPedido($codigoPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        // Obtener el mayor tiempoEstimado y su fecha de alta asociada
        $consultaTiempoMaximo = $objAccesoDato->prepararConsulta(
            "SELECT MAX(tiempoEstimado) as tiempoMaximoEntrega, MAX(fechaAlta) as fechaAlta
            FROM pedidoproducto
            WHERE codigoPedido = :codigoPedido");

        $consultaTiempoMaximo->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consultaTiempoMaximo->execute();
        $resultadoConsulta = $consultaTiempoMaximo->fetch(PDO::FETCH_ASSOC);

        if ($resultadoConsulta && $resultadoConsulta['tiempoMaximoEntrega'] && $resultadoConsulta['fechaAlta']) {
            // Guardar el tiempo máximo y la fecha de alta
            $tiempoMaximoEntrega = DateTime::createFromFormat('H:i:s', $resultadoConsulta['tiempoMaximoEntrega']);
            $fechaAlta = new DateTime($resultadoConsulta['fechaAlta']);

            // Obtener el horario actual
            $fechaActual = new DateTime();

            // Calcular la diferencia de tiempo entre la fecha de alta y el horario actual
            $diferenciaTiempo = $fechaAlta->diff($fechaActual);

            // Restar la diferencia de tiempo al tiempo máximo estimado
            $tiempoRestante = clone $tiempoMaximoEntrega;
            $tiempoRestante->sub($diferenciaTiempo);

            return $tiempoRestante->format('H:i:s'); // Formato HH:MM:SS para el tiempo restante
        }

        return "No se encontró información para calcular la demora del pedido.";
    }
    
    
    


    
    
    
}


?>