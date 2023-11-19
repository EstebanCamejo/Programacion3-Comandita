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
    public function __construct(){}

    public function crearPedidoProducto()
    {
        $accesoDb = AccesoDatos::obtenerInstancia();

        $consultaTiempoPreparacion = $accesoDb->prepararConsulta("SELECT tiempoPreparacion 
        FROM producto WHERE id = :idProducto");
        $consultaTiempoPreparacion->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consultaTiempoPreparacion->execute();
        $tiempoPreparacionResultado = $consultaTiempoPreparacion->fetch(PDO::FETCH_ASSOC);
        $tiempoPreparacion = $tiempoPreparacionResultado['tiempoPreparacion'];
        
        $consulta = $accesoDb->prepararConsulta("INSERT INTO pedidoproducto 
        (codigoPedido, idProducto, tiempoEstimado, codigoMesa, estado, fechaAlta, 
        fechaModificacion,fechaBaja,activo) 
        VALUES (:codigoPedido, :idProducto, :tiempoEstimado, :codigoMesa, :estado,:fechaAlta, 
        :fechaModificacion, :fechaBaja, :activo)");
        
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoEstimado', $tiempoPreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', $this->fechaModificacion, PDO::PARAM_STR);
        $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);
        $consulta->bindValue(':activo', $this->activo, PDO::PARAM_INT);
        
        $this->actualizarPrecioFinal($this->codigoPedido);
        $consulta->execute();     
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
        pp.fechaBaja
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
            pp.fechaBaja
            FROM pedidoproducto pp
            INNER JOIN producto p ON pp.idProducto = p.id
            INNER JOIN estadopedidoproducto ep ON pp.estado = ep.id
            WHERE pp.estado = 1
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
    
   


    public static function borrarPedidoProducto($codigoPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $fechaBaja = date('Y-m-d H:i:s');
        $nuevoEstado = 0;
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoproducto SET activo = :activo,
        fechaBaja = :fechaBaja
        WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fechaBaja, PDO::PARAM_INT);
        
        self::actualizarPrecioFinal($codigoPedido);

        $consulta->execute();
    }



    public static function modificarPedidoProducto($pedidoProducto)
    {       
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $fechaModificacion = date('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoproducto 
        SET codigoPedido = :codigoPedido, 
        idProducto = :idProducto,
        tiempoEstimado = :tiempoEstimado,
        codigoMesa = :codigoMesa,
        fechaModificacion = :fechaModificacion,
        estado = :estado        
        WHERE id = :id");

        $consulta->bindValue(':codigoPedido', $pedidoProducto->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $pedidoProducto->idProducto, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $pedidoProducto->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $pedidoProducto->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR);
        $consulta->bindValue(':id', $pedidoProducto->id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $pedidoProducto->estado, PDO::PARAM_INT);
        
        self::actualizarPrecioFinal($pedidoProducto->codigoPedido);
        self::chequeoPedido($pedidoProducto->codigoPedido);
        $consulta->execute();
    }

    public static function chequeoPedido($codigoPedido) {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
    
        // Verificar si todas las filas con el mismo códigoPedido tienen estado 3
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

    public static function actualizarPrecioFinal($codigoPedido)
    {
        $accesoDb = AccesoDatos::obtenerInstancia();

        // Recuperar el código de pedido actual
       // $codigoPedido = $this->codigoPedido; // Reemplaza esto con la forma en que obtienes tu código de pedido
       
        // Obtener la suma de precios de los productos para el código de pedido dado

        $consultaSumaPrecios = $accesoDb->prepararConsulta("SELECT SUM(pr.precio) AS totalPrecio
        FROM producto pr
        INNER JOIN pedidoproducto pp ON pr.id = pp.idProducto
        WHERE pp.codigoPedido = :codigoPedido AND pp.activo = 1");

        $consultaSumaPrecios->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consultaSumaPrecios->execute();
        $resultado = $consultaSumaPrecios->fetch(PDO::FETCH_ASSOC);

        $totalPrecio = $resultado['totalPrecio'] ?? 0; // Valor predeterminado si no hay resultados

        // Actualizar el precioFinal en la tabla pedido
 
        $consultaActualizarPrecio = $accesoDb->prepararConsulta("UPDATE pedido 
        SET precioFinal = :totalPrecio
        WHERE codigoPedido = :codigoPedido");
        $consultaActualizarPrecio->bindValue(':totalPrecio', $totalPrecio, PDO::PARAM_INT);
        $consultaActualizarPrecio->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consultaActualizarPrecio->execute();
    }
}

?>