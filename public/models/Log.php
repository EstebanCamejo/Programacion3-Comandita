<?php


class Log {

    public $id;
    public $nombre;
    public $sector;
    public $dni;
    public $tipoUsuario;
    public $fecha;
    public $metodo;
    public $url;
    
    public function __construct() {}

    public function crearUno() {

        $fechaActual = date('Y-m-d H:i:s');
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (nombre, sector, dni, tipoUsuario, 
        fecha, metodo, url) 
        VALUES (:nombre, :sector, :dni, :tipoUsuario, :fecha, :metodo, :url)");
        $consulta -> bindParam(':nombre', $this->nombre);
        $consulta -> bindParam(':sector', $this->sector);        
        $consulta -> bindParam(':dni', $this->dni);
        $consulta -> bindParam(':tipoUsuario', $this->tipoUsuario);
        $consulta -> bindParam(':fecha', $fechaActual);
        $consulta -> bindParam(':metodo', $this->metodo);
        $consulta -> bindParam(':url', $this->url);
        
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logs;");
        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Log');
    }


    public static function CantidadDeOperacionesPorSector()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        // Consulta para contar las operaciones por sector y obtener el nombre del sector
        $consulta = $objAccesoDatos->prepararConsulta("SELECT s.sector 
            AS nombre_sector, COUNT(l.id) 
            AS cantidad_operaciones
            FROM logs l
            INNER JOIN sectores s ON l.sector = s.id
            GROUP BY l.sector
        ");
    
        $consulta->execute();
    
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function CantidadDeOperacionesPorEmpleadoYSector()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        // Consulta para contar las operaciones por empleado y sector, incluyendo el nombre del empleado
        $consulta = $objAccesoDatos->prepararConsulta("SELECT s.sector 
            AS nombre_sector, u.nombre 
            AS nombre_empleado, l.dni, 
            COUNT(l.id) AS cantidad_operaciones
            FROM logs l
            INNER JOIN sectores s ON l.sector = s.id
            INNER JOIN usuario u ON l.dni = u.dni
            GROUP BY l.dni, l.sector
            ORDER BY nombre_sector ASC
        ");
    
        $consulta->execute();
    
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
   
    
    public static function DiasYHorariosPorEmpleado($dni)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia(); 
    
        $consulta = $objAccesoDatos->prepararConsulta("SELECT logs.fecha
            FROM logs
            WHERE logs.dni = :dni
            ORDER BY logs.fecha
        ");
        
        $consulta->bindValue(':dni', $dni, PDO::PARAM_INT);
        $consulta->execute();
    
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
        $consultaEmpleado = $objAccesoDatos->prepararConsulta("SELECT usuario.nombre, 
            sectores.sector 
            AS nombre_sector
            FROM usuario
            JOIN sectores ON usuario.sector = sectores.id
            WHERE usuario.dni = :dni
        ");
    
        $consultaEmpleado->bindValue(':dni', $dni, PDO::PARAM_INT);
        $consultaEmpleado->execute();
    
        $empleado = $consultaEmpleado->fetch(PDO::FETCH_ASSOC);
    
        $empleado['dias_y_horarios'] = $resultados;
    
        return $empleado;
    }
    
}
?>

