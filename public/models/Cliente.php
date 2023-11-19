<?php

class Cliente
{
    public $id;
    public $nombre;    

    public function __construct() { }
   
    public function crearCliente()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO cliente (nombre) VALUES (:nombre)");
              
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
     
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function VerTiempoEstimadoMaximo($codigoPedido, $codigoMesa)
    {
        $accesoDb = AccesoDatos::obtenerInstancia();

        $consulta = $accesoDb->prepararConsulta("SELECT MAX(tiempoEstimado) AS TiempoEstimadoMaximo 
        FROM pedidoproducto 
        WHERE codigoPedido = :codigoPedido 
        AND codigoMesa = :codigoMesa");

        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();
        $tiempoEstimadoMaximo = $consulta->fetch(PDO::FETCH_ASSOC)['TiempoEstimadoMaximo'];

        
        return $tiempoEstimadoMaximo;
    }
}
?>