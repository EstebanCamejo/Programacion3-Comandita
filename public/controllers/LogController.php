<?php 

class LogController
{

    public function CantidadDeOperacionesPorSector($request, $response, $args)
    {
        $lista = Log::CantidadDeOperacionesPorSector();

        if(!empty($lista))
        {
          $payload = json_encode(array("Cantidad de operaciones por sector" => $lista),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("Error al traer la lista de operaciones"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function CantidadDeOperacionesPorEmpleadoYSector($request, $response, $args)
    {
        $lista = Log::CantidadDeOperacionesPorEmpleadoYSector();

        if(!empty($lista))
        {
          $payload = json_encode(array("Cantidad de operaciones por empleado y sector" => $lista),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("Error al traer la lista de operaciones"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function EmpleadoDiasYHorarios($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $dni = $parametros['dni'];
    
        $informacionEmpleado = Log::DiasYHorariosPorEmpleado($dni);
    
        if (!empty($informacionEmpleado)) {
            $payload = json_encode(
                array(
                    "Empleado" => array(
                        "nombre" => $informacionEmpleado['nombre'],                        
                        "sector" => $informacionEmpleado['nombre_sector']),                    
                        "Dias y horarios del empleado" => $informacionEmpleado['dias_y_horarios'])
                        ,JSON_PRETTY_PRINT);                
        } else {
            $payload = json_encode(array("Error al obtener la informacion del empleado"), JSON_PRETTY_PRINT);
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
}
    
    
?>