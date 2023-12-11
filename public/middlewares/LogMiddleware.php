<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;


require_once './models/Log.php';

class LogMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {    
        $log = new Log();
        $log->nombre = 'anonimo';
        $log->dni = null;
        $log->sector = null;
        $log->tipoUsuario = null;

        $header = $request->getHeaderLine('Authorization');   
                
        if(!empty($header)){            
            $token = trim(explode("Bearer", $header)[1]);
            $data = AutentificadorJWT::ObtenerData($token);
                    
            $log->nombre = $data->nombre;          
            $log->dni = $data->dni;
            $log->sector = $data->sector;           
            $log->tipoUsuario = $data->tipoUsuario;                          
        }        

        $log->url = $request->getUri()->getPath();
        $log->metodo = $request->getMethod();
        $log->crearUno();

        $response = $handler->handle($request);
        return $response;
    }   
}
?>