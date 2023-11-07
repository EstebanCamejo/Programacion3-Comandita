<?php 

class Pedido
{
    public $id;
    public $codigoPedido;
    public $idCliente;
    public $codigoMesa;
    public $idEmpleado;
    public $fechaPreparacion;
    public $precioFinal;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
         // Genera un código alfanumérico de 5 caracteres
        $codigoAlfanumerico = Pedido::generarCodigoAlfanumerico(5);
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (codigoPedido, idCliente, codigoMesa, 
        idEmpleado, fechaPreparacion, precioFinal) 
        VALUES (:codigoPedido, :idCliente, :codigoMesa, :idEmpleado, :fechaPreparacion, :precioFinal)");           
        $consulta->bindValue(':codigoPedido', $this->codigoPedido = $codigoAlfanumerico);
        $consulta->bindValue(':idCliente', $this->idCliente, PDO::PARAM_INT);    
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);   
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);   
        $consulta->bindValue(':fechaPreparacion', $this->fechaPreparacion, PDO::PARAM_INT);   
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
        }
    
        return $codigo;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedido.id, pedido.codigoPedido, pedido.idCliente, 
        pedido.codigoMesa, pedido.idEmpleado, pedido.fechaPreparacion, pedido.precioFinal");

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
}


?>