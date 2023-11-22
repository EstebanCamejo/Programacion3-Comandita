<?php


    class Encuesta{

        public $id;
        public $puntuacionMozo;
        public $puntuacionCocina;
        public $puntuacionMesa;
        public $puntuacionBebidas;
        public $comentario;
        public $codigoMesa;
        public $codigoPedido;
        public $fechaAlta;
        public $activo;



        public function crearEncuesta()
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta 
            (puntuacionMozo, puntuacionCocina, puntuacionMesa, puntuacionBebidas,
            comentario, codigoMesa, codigoPedido, fechaAlta, activo)
            VALUES (:puntuacionMozo, :puntuacionCocina, :puntuacionMesa, :puntuacionBebidas, :comentario, 
            :codigoMesa, :codigoPedido, :fechaAlta, :activo)");
        
            $consulta->bindValue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_INT);
            $consulta->bindValue(':puntuacionCocina', $this->puntuacionCocina, PDO::PARAM_INT);
            $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_INT);
            $consulta->bindValue(':puntuacionBebidas', $this->puntuacionBebidas, PDO::PARAM_INT);
            $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
            $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);        
            $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
            $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);                    
            $consulta->bindValue(':activo', $this->activo, PDO::PARAM_INT);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function mejoresComentarios() {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
            $consulta = $objAccesoDatos->prepararConsulta("SELECT codigoMesa, 
            AVG((puntuacionMozo + puntuacionCocina + puntuacionMesa + puntuacionBebidas) / 4) 
            AS promedio
            FROM encuesta
            GROUP BY codigoMesa
            ORDER BY promedio DESC
            LIMIT 3");

            $consulta->execute();
            $mejoresMesas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        
            $resultado = [];
        
            foreach ($mejoresMesas as $mesa) {
                $codigoMesa = $mesa['codigoMesa'];
        
                $consulta = $objAccesoDatos->prepararConsulta("SELECT fechaAlta, comentario, 
                puntuacionMozo, puntuacionCocina, puntuacionMesa, puntuacionBebidas
                FROM encuesta
                WHERE codigoMesa = :codigoMesa
                ORDER BY ((puntuacionMozo + puntuacionCocina + puntuacionMesa + puntuacionBebidas) / 4) 
                DESC");
        
                $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
                $consulta->execute();
                $comentario = $consulta->fetch(PDO::FETCH_ASSOC);
        
                if ($comentario) {
                    $resultado[] = [
                        'codigoMesa' => $codigoMesa,
                        'fechaAlta' => $comentario['fechaAlta'],
                        'comentario' => $comentario['comentario'],
                        'puntuacionMozo' => $comentario['puntuacionMozo'],
                        'puntuacionCocina' => $comentario['puntuacionCocina'],
                        'puntuacionMesa' => $comentario['puntuacionMesa'],
                        'puntuacionBebidas' => $comentario['puntuacionBebidas']
                    ];
                }
            }
        
            return $resultado;
        }
        
    }

?>