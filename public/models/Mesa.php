<?php 

class Mesa
{
    public $id;
    public $codigoMesa;    
    public $estadoMesa;
    public $fechaAlta;
    public $fechaModificacion;
    public $fechaBaja;
    public $activo;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
         // Genera un código alfanumérico de 5 caracteres
        $codigoAlfanumerico = Mesa::generarCodigoAlfanumerico(5);
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (codigoMesa, estadoMesa
        ,fechaAlta, fechaModificacion, fechaBaja, activo) 
        VALUES (:codigoMesa, :estadoMesa, :fechaAlta, :fechaModificacion, :fechaBaja, :activo)");           
        $consulta->bindValue(':codigoMesa', $this->codigoMesa = $codigoAlfanumerico);
        $consulta->bindValue(':estadoMesa', $this->estadoMesa, PDO::PARAM_INT);    
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);    
        $consulta->bindValue(':fechaModificacion', $this->fechaModificacion, PDO::PARAM_STR);    
        $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);   
        $consulta->bindValue(':activo', $this->activo, PDO::PARAM_INT);  
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT
                mesa.id AS id, 
                mesa.codigoMesa AS codigoMesa, 
                estadomesa.estado AS estadoMesa,
                mesa.fechaAlta AS fechaAlta,
                mesa.fechaModificacion AS fechaModificacion,
                mesa.fechaBaja AS fechaBaja,
                mesa.activo AS activo
            FROM 
                mesa 
            JOIN 
                estadomesa ON mesa.estadoMesa = estadomesa.id 
            WHERE 
                mesa.activo = 1"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
    
    public static function obtenerMesa($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        mesa.id, mesa.codigoMesa, estadomesa.estado as estadoMesa
        FROM mesa 
        JOIN estadomesa ON mesa.estadoMesa = estadomesa.id
        WHERE mesa.codigoMesa = :codigoMesa AND mesa.activo = 1");

        $consulta->bindValue(':codigoMesa', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    
    public static function cerrarMesa($codigoMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 4;
        $fechaModificacion =  date ('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estadoMesa = :estadoMesa,
        fechaModificacion = :fechaModificacion  
        WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estadoMesa', $nuevoEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->rowCount()>0;
    }
    
    public static function obtenerCodigoMesaPorCodigoPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedido.codigoMesa
            FROM pedido 
            WHERE pedido.codigoPedido = :codigoPedido AND pedido.activo = 1"
        );
        $consulta->bindValue(':codigoPedido', $codigo, PDO::PARAM_STR);
        $consulta->execute();
    
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        if ($resultado) {
            return $resultado['codigoMesa'];
        }
    
        return null; // Devolver null si no se encontró ningún código de mesa para el pedido dado
    }
    public static function borrarMesa($codigoMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 0;
        $fechaBaja =  date ('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET activo = :activo,
        fechaBaja = :fechaBaja  
        WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fechaBaja, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->rowCount()>0;
    }

    public static function modificarMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $fechaModificacion =  date ('Y-m-d H:i:s');

        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa 
        SET codigoMesa = :codigoMesa, 
        estadoMesa = :estadoMesa,
        fechaModificacion = :fechaModificacion
        WHERE id = :id");

        $consulta->bindValue(':codigoMesa', $mesa->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estadoMesa', $mesa->estadoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_INT);

        $consulta->bindValue(':id', $mesa->id, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->rowCount()>0;
    }

    public static function facturacionAscendente()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT mesa.codigoMesa, 
            SUM(pedido.precioFinal) 
            AS total_facturado, usuario.nombre AS mozo_atendio
            FROM mesa
            LEFT JOIN pedido ON mesa.codigoMesa = pedido.codigoMesa
            LEFT JOIN usuario ON pedido.idEmpleado = usuario.id
            WHERE mesa.activo = 1 AND pedido.activo = 1
            GROUP BY mesa.codigoMesa
            ORDER BY mesa.codigoMesa ASC, total_facturado ASC
        ");

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listadoFacturacionEntreFechas($codigoMesa, $fechaInicio, $fechaFin)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();

    $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(precioFinal) 
        AS facturacion_total
        FROM pedido
        WHERE codigoMesa = :codigoMesa 
        AND fechaAlta BETWEEN STR_TO_DATE(:fechaInicio, '%d-%m-%Y') AND STR_TO_DATE(:fechaFin, '%d-%m-%Y')
        AND activo = 1
    ");

    $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
    $consulta->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
    $consulta->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
    $consulta->execute();

    return $consulta->fetch(PDO::FETCH_ASSOC)['facturacion_total'];
}

}

?>