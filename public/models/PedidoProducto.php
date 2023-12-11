<?php 

class PedidoProducto
{
    public $id;
    public $codigoPedido;
    public $idProducto;
    public $tiempoEstimado;
    public $codigoMesa;
    public $fechaAlta;
    public $fechaModificacion;
    public $fechaBaja;
    public $estado;
    public $activo;
    public $sector;
    public $nombre;
    
    public function __construct(){}

    public function crearPedidoProducto()
    {
        $accesoDb = AccesoDatos::obtenerInstancia();
        
        $consulta = $accesoDb->prepararConsulta("INSERT INTO pedidoproducto 
        (codigoPedido, idProducto, tiempoEstimado, sector, codigoMesa, estado, fechaAlta, 
        fechaModificacion, fechaBaja, activo, nombre) 
        VALUES (:codigoPedido, :idProducto, :tiempoEstimado, :sector, :codigoMesa, :estado,:fechaAlta, 
        :fechaModificacion, :fechaBaja, :activo, :nombre)");
        
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', $this->fechaModificacion, PDO::PARAM_STR);
        $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);
        $consulta->bindValue(':activo', $this->activo, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);

        
        $consulta->execute();     
        $this->actualizarPrecioFinalPedido($this->codigoPedido);
    }


    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();      
        $consulta = $objAccesoDatos->prepararConsulta //("SELECT * FROM pedidoproducto WHERE activo = 1");
        ( "SELECT pp.id, 
        pp.codigoPedido, 
        pp.idProducto, 
        pp.tiempoEstimado, 
        pp.codigoMesa,
        ep.estado AS estado,
        pp.fechaAlta,
        pp.fechaModificacion,
        pp.fechaBaja,
        pp.activo,
        pp.sector,
        pp.nombre
        FROM pedidoproducto pp
        INNER JOIN estadopedidoproducto ep ON pp.estado = ep.id
        WHERE pp.activo = 1");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function obtenerPedidosPorSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();      

        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pp.id, 
            pp.codigoPedido, 
            pp.idProducto, 
            pp.tiempoEstimado, 
            pp.codigoMesa,
            ep.estado AS estado,
            pp.fechaAlta,
            pp.fechaModificacion,
            pp.fechaBaja,
            pp.sector,
            pp.nombre,
            pp.activo
            FROM pedidoproducto pp
            INNER JOIN producto p ON pp.idProducto = p.id
            INNER JOIN estadopedidoproducto ep ON pp.estado = ep.id
            WHERE (pp.estado = 1 OR pp.estado = 2)
            AND p.sector = :sector
            AND pp.activo = 1");

        $consulta->bindValue(':sector', $sector, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }
    
    public static function obtenerPedidoProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta
       ("SELECT pp.id, 
        pp.codigoPedido, 
        pp.idProducto, 
        pp.tiempoEstimado, 
        pp.codigoMesa,
        e.estado AS estado,
        pp.fechaAlta,
        pp.fechaModificacion,
        pp.fechaBaja
        FROM pedidoproducto pp
        INNER JOIN estadopedidoproducto e ON pp.estado = e.id
        WHERE pp.id = :id AND pp.activo = 1");
   
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('PedidoProducto');
    }

    public static function borrarPedidoProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();       
        $fechaBaja = date('Y-m-d H:i:s');
        $nuevoEstado = 0;

        $codigoPedido = self::obtenerCodigoPedidoPorId($id);

        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoproducto SET activo = :activo,
        fechaBaja = :fechaBaja
        WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fechaBaja, PDO::PARAM_INT);                

        $consulta->execute();

        self::actualizarPrecioFinalPedido($codigoPedido);

        return $consulta->rowCount() > 0;
    }

    public static function obtenerCodigoPedidoPorId($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
    
        $consulta = $objAccesoDato->prepararConsulta("SELECT codigoPedido 
            FROM pedidoproducto 
            WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        return $resultado['codigoPedido'];
    }

    public static function modificarPedidoProducto($pedidoProducto)
    {       
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $fechaModificacion = date('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoproducto 
        SET 
        tiempoEstimado = :tiempoEstimado,        
        fechaModificacion = :fechaModificacion,
        estado = :estado        
        WHERE id = :id");

        $consulta->bindValue(':tiempoEstimado', $pedidoProducto->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR);
        $consulta->bindValue(':id', $pedidoProducto->id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $pedidoProducto->estado, PDO::PARAM_INT);
        $consulta->execute();
        
        $codigoPedidoProducto = self:: obtenerCodigoPedidoPorId($pedidoProducto->id);
        self::chequeoPedido($codigoPedidoProducto);
        self::actualizarPrecioFinalPedido($codigoPedidoProducto);
        
        return $consulta->rowCount() > 0;
    }

    public static function chequeoPedido($codigoPedido) {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
    
        // Verificar si todas las filas con el mismo codigoPedido tienen estado 3
        $consultaEstado = $objAccesoDato->prepararConsulta(
            "SELECT COUNT(*) AS total
            FROM pedidoproducto
            WHERE codigoPedido = :codigoPedido AND estado != 3"
        );
    
        $consultaEstado->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consultaEstado->execute();
        $resultadoEstado = $consultaEstado->fetch(PDO::FETCH_ASSOC);
    
        if ($resultadoEstado && $resultadoEstado['total'] == 0) {
            // Si todas las filas tienen estado 3, actualizar la tabla pedido
            $consultaActualizarPedido = $objAccesoDato->prepararConsulta(
                "UPDATE pedido
                SET estadoPedido = 2
                WHERE codigoPedido = :codigoPedido"
            );
    
            $consultaActualizarPedido->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
            $consultaActualizarPedido->execute();
            
            return "Se actualizó el estado del pedido a 2.";
        }
    
        return "No se cumple la condición para actualizar el estado del pedido.";
    }

    public static function actualizarPrecioFinalPedido($codigoPedido)
    {
        $accesoDb = AccesoDatos::obtenerInstancia();
    
        // Obtener la suma total de los precios de los productos activos asociados a ese pedido
        $consultaPrecioTotal = $accesoDb->prepararConsulta("SELECT SUM(producto.precio) AS total
            FROM pedidoproducto
            INNER JOIN producto ON pedidoproducto.idProducto = producto.id
            WHERE pedidoproducto.codigoPedido = :codigoPedido
            AND pedidoproducto.activo = 1");
        $consultaPrecioTotal->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consultaPrecioTotal->execute();
        $total = $consultaPrecioTotal->fetchColumn();
    
        // Actualizar el precioFinal en la tabla pedido con la suma total obtenida
        $consultaActualizarPedido = $accesoDb->prepararConsulta("UPDATE pedido 
            SET precioFinal = :total
            WHERE codigoPedido = :codigoPedido");
        $consultaActualizarPedido->bindValue(':total', $total, PDO::PARAM_INT);
        $consultaActualizarPedido->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consultaActualizarPedido->execute();
    }

    public static function obtenerNombrePedidoProductoPorId($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
    
        $consulta = $objAccesoDato->prepararConsulta("SELECT nombre 
            FROM pedidoproducto 
            WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        return $resultado['nombre'];
    }
    
}



?>