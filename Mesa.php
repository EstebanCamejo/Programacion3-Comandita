<?php 

class Mesa
{
    public $id;
    public $codigoMesa;    
    public $estadoMesa;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
         // Genera un código alfanumérico de 5 caracteres
        $codigoAlfanumerico = Mesa::generarCodigoAlfanumerico(5);
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (codigoMesa, estadoMesa) VALUES (:codigoMesa, :estadoMesa)");           
        $consulta->bindValue(':codigoMesa', $this->codigoMesa = $codigoAlfanumerico);
        $consulta->bindValue(':estadoMesa', $this->estadoMesa, PDO::PARAM_INT);    
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
            estadomesa ON mesa.estadoMesa = estadomesa.id");
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
        WHERE mesa.codigoMesa = :codigoMesa");


        $consulta->bindValue(':codigoMesa', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    
    public static function cerrarMesa($codigoMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 4;
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estadoMesa = :estadoMesa  
        WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estadoMesa', $nuevoEstado, PDO::PARAM_INT);

        $consulta->execute();
    }

    public static function modificarMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa 
        SET codigoMesa = :codigoMesa, 
        estadoMesa = :estadoMesa
        WHERE id = :id");

        $consulta->bindValue(':codigoMesa', $mesa->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estadoMesa', $mesa->estadoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':id', $mesa->id, PDO::PARAM_INT);

        $consulta->execute();
    }
}

?>