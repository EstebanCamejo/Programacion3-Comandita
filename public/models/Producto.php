<?php 

    class Producto 
    {
        public $id;
        public $sector;
        public $nombre;  
        public $precio;
        public $tiempoPreparacion;
        public $activo;
        public $fechaAlta;
        public $fechaModificacion;
        public $fechaBaja;

        public function crearProducto()
        {
            $estado = 1;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO producto 
            (sector, nombre, precio, tiempoPreparacion, fechaAlta, fechaModificacion, fechaBaja, activo) 
            VALUES (:sector, :nombre, :precio, :tiempoPreparacion, :fechaAlta, :fechaModificacion, 
            :fechaBaja, :activo)");
            
            $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoPreparacion', $this->tiempoPreparacion, PDO::PARAM_STR);
         
            $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
            $consulta->bindValue(':fechaModificacion', $this->fechaModificacion, PDO::PARAM_STR);
            $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);

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
            producto.fechaAlta,
            producto.fechaModificacion,
            producto.fechaBaja,
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
            producto.fechaAlta,
            producto.fechaModificacion,
            producto.fechaBaja,
            producto.activo
            FROM producto JOIN sectores ON producto.sector = sectores.id
            WHERE producto.id = :id AND producto.activo = 1");
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);
            $consulta->execute();
    
            return $consulta->fetchObject('Producto');
        }
        
        public static function borrarProducto($id)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $fechaBaja =  date ('Y-m-d H:i:s');
            $nuevoEstado = 0;
            $consulta = $objAccesoDato->prepararConsulta("UPDATE producto SET activo = :activo,
              fechaBaja = :fechaBaja
            WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);
            $consulta->bindValue(':fechaBaja', $fechaBaja, PDO::PARAM_STR);
    
            $consulta->execute();
            return $consulta->rowCount() > 0;
        }

        public static function modificarProducto($producto)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $fechaModificacion =  date ('Y-m-d H:i:s');
            $consulta = $objAccesoDato->prepararConsulta("UPDATE producto 
            SET sector = :sector, 
            nombre = :nombre,
            precio = :precio,
            tiempoPreparacion = :tiempoPreparacion,
            fechaModificacion = :fechaModificacion,
            
            WHERE id = :id");
    
            $consulta->bindValue(':sector', $producto->sector, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoPreparacion', $producto->tiempoPreparacion, PDO::PARAM_STR);           
            $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR);
            $consulta->bindValue(':id', $producto->id, PDO::PARAM_INT);

            $consulta->execute();
            return $consulta->rowCount() > 0;
        }

        public static function CargarCSV($archivo)
        {
            $array = manejadorCSV::LeerCsv($archivo);
          
            for($i = 0; $i < sizeof($array); $i++)
            {             
                $campos = explode(",", $array[$i]);               
                
                $producto = new Producto();
                $producto->id = $campos[0];
                $producto->sector = $campos[1];
                $producto->nombre = $campos[2];
                $producto->precio = $campos[3];
                $producto->tiempoPreparacion = $campos[4];
                $producto->activo = $campos[5];
                $producto->fechaAlta = $campos[6];
                $producto->fechaModificacion = $campos[7];
                $producto->fechaBaja = $campos [8];
                
                $producto->crearProducto();
            }
        }

                
        public static function obtenerProductosMasVendidos()
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
            $consulta = $objAccesoDatos->prepararConsulta("SELECT p.nombre 
                AS nombre_producto, 
                COUNT(pp.idProducto) 
                AS cantidad_vendida
                FROM producto p
                LEFT JOIN pedidoproducto pp ON p.id = pp.idProducto
                WHERE p.activo = 1
                GROUP BY p.id, p.nombre
                ORDER BY cantidad_vendida DESC
            ");
        
            $consulta->execute();
        
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        
        
    }


?>