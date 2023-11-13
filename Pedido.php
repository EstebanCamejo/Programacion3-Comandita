<?php 

class Pedido
{
    public $id;
    public $codigoPedido; //ok
    public $idCliente;//ok
    public $codigoMesa; //ok
    public $idEmpleado;//ok
    public $fechaPreparacion;
    public $fechaFinalizacion;    
    public $estadoPedido;
    public $precioFinal;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
         // Genera un código alfanumérico de 5 caracteres
        $codigoAlfanumerico = Pedido::generarCodigoAlfanumerico(5);
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (codigoPedido, idCliente, codigoMesa, 
        idEmpleado, fechaPreparacion, fechaFinalizacion, estadoPedido, precioFinal) 
        VALUES (:codigoPedido, :idCliente, :codigoMesa, :idEmpleado, :fechaPreparacion, :fechaFinalizacion, 
        :estadoPedido, :precioFinal)");           
        $consulta->bindValue(':codigoPedido', $this->codigoPedido = $codigoAlfanumerico);
        $consulta->bindValue(':idCliente', $this->idCliente, PDO::PARAM_INT);    
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);   
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);   
        $consulta->bindValue(':fechaPreparacion', $this->fechaPreparacion, PDO::PARAM_INT);   
        $consulta->bindValue(':fechaFinalizacion', $this->fechaFinalizacion, PDO::PARAM_INT);   
        $consulta->bindValue(':estadoPedido', $this->estadoPedido, PDO::PARAM_INT); 
        $consulta->bindValue(':precioFinal', $this->precioFinal, PDO::PARAM_INT);      
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
        pedido.fechaPreparacion, 
        pedido.fechaFinalizacion, 
        estadopedido.estado as estadoPedido, 
        pedido.precioFinal
        FROM 
            pedido 
        LEFT JOIN 
            estadopedido ON pedido.estadoPedido = estadopedido.id");
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
            pedido.fechaPreparacion, 
            pedido.fechaFinalizacion, 
            estadopedido.estado as estadoPedido, 
            pedido.precioFinal 
            FROM pedido 
            JOIN estadopedido ON pedido.estadoPedido = estadopedido.id 
            WHERE pedido.codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
    
    public static function cancelarPedido($codigoPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 3;
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estadoPedido = :estadoPedido  
        WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estadoPedido', $nuevoEstado, PDO::PARAM_INT);

        $consulta->execute();
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido 
        SET idCliente = :idCliente, 
        codigoMesa = :codigoMesa,
        idEmpleado = :idEmpleado,
        fechaPreparacion = :fechaPreparacion,
        fechaFinalizacion = :fechaFinalizacion,
        estadoPedido = :estadoPedido,
        precioFinal = :precioFinal
        WHERE codigoPedido = :codigoPedido");

        $consulta->bindValue(':idCliente', $pedido->idCliente, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $pedido->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $pedido->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaPreparacion', $pedido->fechaPreparacion, PDO::PARAM_STR);
        $consulta->bindValue(':fechaFinalizacion', $pedido->fechaFinalizacion, PDO::PARAM_STR);
        $consulta->bindValue(':estadoPedido', $pedido->estadoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':precioFinal', $pedido->precioFinal, PDO::PARAM_INT);
        $consulta->bindValue(':codigoPedido', $pedido->codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
    }
}


?>