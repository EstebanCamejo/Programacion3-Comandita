<?php 

class PedidoProducto
{
    public $id;
    public $codigoPedido;
    public $idProducto;
    public $tiempoEstimado;
    public $codigoMesa;
    public $activo;


    public function crearPedidoProducto()
    {
        $estado = 1;
        $accesoDb = AccesoDatos::obtenerInstancia();
        $consulta = $accesoDb->prepararConsulta("INSERT INTO pedidoproducto 
        (codigoPedido, idProducto, tiempoEstimado, codigoMesa, activo) 
        VALUES (:codigoPedido, :idProducto, :tiempoEstimado, :codigoMesa, :activo)");
        
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $this->activo = $estado);
        $consulta->execute();
     
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();      
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidoproducto WHERE activo = 1");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function obtenerPedidoProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedidoproducto.id, 
            pedidoproducto.codigoPedido, 
            pedidoproducto.idProducto, 
            pedidoproducto.tiempoEstimado, 
            pedidoproducto.codigoMesa,
            pedidoproducto.activo,
            FROM pedidoproducto             
            WHERE pedidoproducto.id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('PedidoProducto');
    }

    public static function borrarPedidoProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 0;
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoproducto SET activo = :activo  
        WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);

        $consulta->execute();
    }
    
}






?>