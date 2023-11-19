<?php
require_once './models/Usuario.php';

class UsuarioController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];
        $tipoUsuario = $parametros['tipoUsuario'];
        $dni = $parametros['dni'];
        $estado = $parametros['estado'];

        // Creamos el usuario
        $usuario = new Usuario();
        $usuario->nombre = $nombre;
        $usuario->clave = $clave;
        $usuario->tipoUsuario = $tipoUsuario;
        $usuario->dni = $dni;
        $usuario->estado = $estado;
        $usuario->fechaAlta = date ('Y-m-d H:i:s');
        $usuario->fechaModificacion = date (':iY-m-d H:s');//"0000-00-00";

        $usuario->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['dni'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $usuarioId = $args["id"];       

        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BajarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuarioId = $parametros["id"];
        Usuario::bajaUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario de baja con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];        
        $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];
        $tipoUsuario = $parametros['tipoUsuario'];
        $dni = $parametros['dni'];
        $estado = $parametros['estado'];

        // Creamos el usuario
        $usuario = new Usuario();
        $usuario->id = $id;
        $usuario->nombre = $nombre;
        $usuario->clave = $clave;
        $usuario->tipoUsuario = $tipoUsuario;
        $usuario->dni = $dni;
        $usuario->estado = $estado;
        $usuario->fechaModificacion = date ('Y-m-d H:i:s');

        Usuario::modificarUsuario($usuario);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Login ($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $dni = $parametros['dni'];        
        $clave = $parametros['clave'];

        // Creamos el usuario
        $usuario = new Usuario();
        $usuario->dni = $dni;
        $usuario->clave = $clave;     

        Usuario::validarUsuario($usuario);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}