<?php

class Usuario
{
    public $id;
    public $nombre;
    public $clave;
    public $tipoUsuario;
    public $dni;
    public $fechaAlta;
    public $fechaModificacion;
    public $estado;
    public $activo;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuario (nombre, clave, tipoUsuario, dni,
         fechaAlta, fechaModificacion, estado, activo)
         VALUES (:nombre, :clave, :tipoUsuario, :dni, :fechaAlta, :fechaModificacion, :estado, :activo)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':tipoUsuario', $this->tipoUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':dni', $this->dni, PDO::PARAM_INT);
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', $this->fechaModificacion, PDO::PARAM_STR);        
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $this->activo, PDO::PARAM_INT);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
           " SELECT 
            usuario.id, 
            usuario.nombre, 
            usuario.clave, 
            tipousuario.Puesto as tipoUsuario, 
            usuario.dni, 
            usuario.fechaAlta, 
            usuario.fechaModificacion,
            usuario.fechaBaja,
            estadousuario.estado
        FROM 
            usuario 
        LEFT JOIN 
            tipousuario ON usuario.tipoUsuario = tipousuario.id 
        LEFT JOIN 
            estadousuario ON usuario.estado = estadousuario.id
        WHERE  usuario.activo = 1");      
       $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();     
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT usuario.id, 
            usuario.nombre, 
            usuario.clave, 
            tipousuario.Puesto as tipoUsuario, 
            usuario.dni, 
            usuario.fechaAlta, 
            usuario.fechaModificacion,
            usuario.fechaBaja,
            usuario.estado 
        FROM usuario 
        JOIN tipousuario ON usuario.tipoUsuario = tipousuario.id 
        WHERE usuario.dni = :dni AND usuario.activo = 1");
        $consulta->bindValue(':dni', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function borrarUsuario($usuarioId)
    {        
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 0;
        $fechaBaja = date ('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario 
        SET activo = :activo,
        fechaBaja = :fechaBaja    
        WHERE id = :id");
        $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $consulta->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fechaBaja, PDO::PARAM_STR);

        $consulta->execute();
    }

    public static function bajaUsuario($usuarioId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 3;
        $fechaModificacion = date ('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario 
        SET estado = :estado,
        fechaModificacion = :fechaModificacion    
        WHERE id = :id");
        $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR);

        $consulta->execute();
    }
    
    
    public static function modificarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $fechaModificacion = date ('Y-m-d H:i:s');
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario 
        SET nombre = :nombre, 
        clave = :clave, 
        tipoUsuario = :tipoUsuario,
        dni = :dni,
        estado = :estado,
        fechaModificacion = :fechaModificacion
        WHERE id = :id");

        $consulta->bindValue(':nombre', $usuario->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $usuario->clave, PDO::PARAM_STR);
        $consulta->bindValue(':tipoUsuario', $usuario->tipoUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':dni', $usuario->dni, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $usuario->estado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaModificacion', $fechaModificacion, PDO::PARAM_STR);
        $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
        $consulta->execute();
                                
        
    }
}