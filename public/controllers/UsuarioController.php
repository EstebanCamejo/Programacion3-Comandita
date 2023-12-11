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
        $usuario->activo = 1;
        $usuario->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito",
        "nombre"=>$usuario->nombre,
        "clave"=>$usuario->clave,
        "tipoUsuario"=>$usuario->tipoUsuario,
        "dni"=>$usuario->dni,
        "estado"=>$usuario->estado,
        "fechaAlta"=>$usuario->fechaAlta),JSON_PRETTY_PRINT);             

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        if(!empty($lista))
        {
          $payload = json_encode(array("lista de todos los usuarios" => $lista),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("Error, la lista de usuarios no se encontro"),JSON_PRETTY_PRINT);
        }       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerUno($request, $response, $args)
    {
        $usr = $args['dni'];
        $usuario = Usuario::obtenerUsuario($usr);
        if(!empty($usuario))
        {
          $payload = json_encode(array("Usuario encontrado"=>$usuario),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("Usuario no encontrado"),JSON_PRETTY_PRINT);
        }      

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {       
        $usuarioId = $args["id"];    
        echo($usuarioId);
                
        $usuarioBorrado = Usuario::borrarUsuario($usuarioId);

        if($usuarioBorrado)
        {
          $payload = json_encode(array("mensaje" => "Usuario borrado con exito"),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "Error, usuario no encontrado"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BajarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuarioId = $parametros["id"];
        $usuarioBaja = Usuario::bajaUsuario($usuarioId);

        if($usuarioBaja)
        {
          $payload = json_encode(array("mensaje" => "Usuario de baja con exito"),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("mensaje" => "Error, usuario no encontrado"),JSON_PRETTY_PRINT);
        }
     
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

        $usuarioModificado = Usuario::modificarUsuario($usuario);

        if($usuarioModificado)
        {
          $payload = json_encode(array("mensaje" => "Usuario modificado con exito",
          "nombre"=>$usuario->nombre,
          "clave"=>$usuario->clave,
          "tipoUsuario"=>$usuario->tipoUsuario,
          "dni"=>$usuario->dni,
          "estado"=>$usuario->estado,
          "fechaModificacion"=>$usuario->fechaModificacion),JSON_PRETTY_PRINT);          
        }else
        {
          $payload = json_encode(array("mensaje" => "Error, usuario no modificado"),JSON_PRETTY_PRINT);
        }      

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

        $usuario = Usuario::validarUsuario($usuario);
        if($usuario == null)
        {
          $payload = json_encode(array("mensaje" => "Usuario inexistente."),JSON_PRETTY_PRINT);
        }
        else
        {          
          $datos = array('nombre'=>$usuario->nombre, 'dni'=>$usuario->dni,
          'sector'=>$usuario->sector,'tipoUsuario'=>$usuario->tipoUsuario);          
          $token = AutentificadorJWT::CrearToken($datos);
          $payload = json_encode(array('jwt'=>$token));          
        }

       // var_dump($datos);
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    /*
    public function guardarLogoPdf ($request, $response, $args)
    {
      $archivoGuardado = Usuario::GeneratePdfFile("logoPdf");

      if($archivoGuardado)
      {
        $payload = json_encode(array("mensaje" => "Archivo guardado con exito."), JSON_PRETTY_PRINT);
      }else
      {
        $payload = json_encode(array("mensaje" => "Error al generar el PDF."),JSON_PRETTY_PRINT);
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    */
}