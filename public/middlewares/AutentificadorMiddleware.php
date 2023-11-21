<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class AutentificadorMiddleware
{
  
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getQueryParams();

        $sector = $parametros['sector'];

        if ($sector === 'admin') {
            $response = $handler->handle($request);
        } 
        else 
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No sos Admin'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarToken(Request $request, RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine('Authorization');
        if($header)
        {
            $token = trim(explode("Bearer", $header)[1]);
            try{                       
                AutentificadorJWT::VerificarToken($token);
                $response = $handler->handle($request);
            }
            catch (Exception $e)
            {
                $response = new Response();
                $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN !!',"excepcion"=>$e));
                $response->getBody()->write($payload);
            }
        }
        else 
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: No hay seteada una autorizacion'));
            $response->getBody()->write( $payload);
        }
        
        return $response->withHeader('Content-Type','application/json');
    }


    public static function verificarRolSocio(Request $request, RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine('Authorization');
      
        $token = trim(explode("Bearer", $header)[1]);
        try{
            AutentificadorJWT::VerificarToken($token);

            //////
            $data = AutentificadorJWT::ObtenerData($token);
            /// si es un socio
            if ($data->tipoUsuario === 1)
            {   
                //aca propaga el middleware a otro
                $request->datosToken= $data;

                $response = $handler->handle($request);
            } // si no es socio tiro una excepcion
            else
            {
                throw new Exception();
            }          
        }
        catch (Exception $e)
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Usuario no autorizado'));
            $response->getBody()->write( $payload);
        }
       
      
        return $response->withHeader('Content-Type','application/json');
    }


    public static function verificarRolMozo(Request $request, RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine('Authorization');
      
        $token = trim(explode("Bearer", $header)[1]);
        try{
            AutentificadorJWT::VerificarToken($token);

            //////
            $data = AutentificadorJWT::ObtenerData($token);
            /// si es un socio
            if ($data->tipoUsuario === 2)
            {   
                //aca propaga el middleware a otro
                $request->datosToken= $data;

                $response = $handler->handle($request);
            } // si no es socio tiro una excepcion
            else
            {
                throw new Exception();
            }          
        }
        catch (Exception $e)
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Usuario no autorizado'));
            $response->getBody()->write( $payload);
        }
       
      
        return $response->withHeader('Content-Type','application/json');
    }

    public static function verificarRolOtrosUsuarios(Request $request, RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine('Authorization');
      
        $token = trim(explode("Bearer", $header)[1]);
        try{
            AutentificadorJWT::VerificarToken($token);

            //////
            $data = AutentificadorJWT::ObtenerData($token);
            /// si es un socio
            if ($data->tipoUsuario >= 3)
            {   
                //aca propaga el middleware a otro
                $request->datosToken= $data;

                $response = $handler->handle($request);
            } // si no es socio tiro una excepcion
            else
            {
                throw new Exception();
            }          
        }
        catch (Exception $e)
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Usuario no autorizado'));
            $response->getBody()->write( $payload);
        }
       
      
        return $response->withHeader('Content-Type','application/json');
    }



    
}

?>
