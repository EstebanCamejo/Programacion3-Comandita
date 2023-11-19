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
      //$consulta = $objAccesoDatos->prepararConsulta("SELECT mesa.id, mesa.codigoMesa, mesa.estadoMesa 
      //FROM mesa JOIN estadomesa ON mesa.estadoMesa = estadomesa.estado");
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        mesa.id, 
        mesa.codigoMesa, 
        estadomesa.estado as estadoMesa 
        FROM 
            mesa 
        JOIN 
            estadomesa ON mesa.estadoMesa = estadomesa.id WHERE mesa.activo = 1");
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
    }
}

?>