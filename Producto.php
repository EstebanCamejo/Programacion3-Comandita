<?php 
    class Producto 
    {
        public $id;
        public $sector;
        public $nombre;  
        public $precio;
        public $tiempoPreparacion;
        public $activo;

        public function crearProducto()
        {
            $estado = 1;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO producto 
            (sector, nombre, precio, tiempoPreparacion, activo) 
            VALUES (:sector, :nombre, :precio, :tiempoPreparacion, :activo)");
            
            $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoPreparacion', $this->tiempoPreparacion, PDO::PARAM_STR);
            $consulta->bindValue(':activo', $this->activo = $estado);
            $consulta->execute();    

            return $objAccesoDatos->obtenerUltimoId();
        }
    
        public static function obtenerTodos()
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia(); 
            $consulta = $objAccesoDatos->prepararConsulta("SELECT 
            producto.id, 
            sectores.sector as sector, 
            producto.nombre, 
            producto.precio, 
            producto.tiempoPreparacion,
            producto.activo
            FROM producto JOIN sectores ON producto.sector = sectores.id WHERE activo = 1");
            $consulta->execute();
    
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');    
        }
        
        public static function obtenerProducto($id)
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT 
            producto.id, 
            sectores.sector as sector, 
            producto.nombre, 
            producto.precio, 
            producto.tiempoPreparacion,
            producto.activo
            FROM producto JOIN sectores ON producto.sector = sectores.id
            WHERE producto.id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);
            $consulta->execute();
    
            return $consulta->fetchObject('Producto');
        }
        
        public static function borrarProducto($id)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $nuevoEstado = 0;
            $consulta = $objAccesoDato->prepararConsulta("UPDATE producto SET activo = :activo  
            WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);
    
            $consulta->execute();
        }

        public static function modificarProducto($producto)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE producto 
            SET sector = :sector, 
            nombre = :nombre,
            precio = :precio,
            tiempoPreparacion = :tiempoPreparacion,
            activo = :activo
            WHERE id = :id");
    
            $consulta->bindValue(':sector', $producto->sector, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoPreparacion', $producto->tiempoPreparacion, PDO::PARAM_STR);
            $consulta->bindValue(':activo', $producto->activo, PDO::PARAM_INT);
            $consulta->bindValue(':id', $producto->id, PDO::PARAM_INT);

            $consulta->execute();
        }
    }


?>