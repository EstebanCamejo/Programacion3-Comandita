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

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuario (nombre, clave, tipoUsuario, dni, fechaAlta, fechaModificacion,estado)
         VALUES (:nombre, :clave, :tipoUsuario, :dni, :fechaAlta, :fechaModificacion,:estado)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':tipoUsuario', $this->tipoUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':dni', $this->dni, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', $this->fechaModificacion, PDO::PARAM_STR);        
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
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
            estadousuario.estado
        FROM 
            usuario 
        LEFT JOIN 
            tipousuario ON usuario.tipoUsuario = tipousuario.id 
        LEFT JOIN 
            estadousuario ON usuario.estado = estadousuario.id");      
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
            usuario.estado 
        FROM usuario 
        JOIN tipousuario ON usuario.tipoUsuario = tipousuario.id 
        WHERE dni = :dni");
        $consulta->bindValue(':dni', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $nuevoEstado = 3;
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET estado = :estado  WHERE id = :id");
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_INT);

        $consulta->execute();
    }
    
    
    public static function modificarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
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
        $consulta->bindValue(':dni', $usuario->dni, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $usuario->estado, PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', $usuario->fechaModificacion, PDO::PARAM_INT);
        $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
        $consulta->execute();
    }
}