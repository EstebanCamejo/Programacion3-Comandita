<?php 
    class Producto 
    {
        public $id;
        public $sector;
        public $nombre;  
        public $precio;
        public $tiempoPreparacion;


        public function crearProducto()
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO producto (sector, nombre, precio, tiempoPreparacion) 
            VALUES (:sector, :nombre, :precio, :tiempoPreparacion)");
            
            $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoPreparacion', $this->tiempoPreparacion, PDO::PARAM_STR);
            $consulta->execute();    

            return $objAccesoDatos->obtenerUltimoId();
        }
    
        public static function obtenerTodos()
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT producto.id, producto.sector, producto.nombre, producto.precio, producto.tiempoPreparacion
             FROM producto JOIN sectores ON producto.sector = sectores.id");
            $consulta->execute();
    
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');    
        }


    }


?>