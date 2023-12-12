<?php
require('./fpdf/fpdf.php');

class PDF extends FPDF
{
// Cabecera de página
 
    function Header()
    {
        // Logo
        $this->Image('logo.png',36,40,155);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Movernos a la derecha
        $this->Cell(60);
        $this->Cell(80,10,'Logo Green Beer',1,0,'C');
        // Título       
        // Salto de línea
        $this->Ln(20);
    }
    
    public static function CrearPDF()
    {

        // Creación del objeto de la clase heredada
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',12);

        $pdf->Output('F', 'logo.pdf');

        $nombreArchivo = 'logo.pdf';
        $carpetaDestino = './Logo/';

        // Verifica si la carpeta de destino existe
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        // Mueve el archivo a la carpeta de destino
        $rutaCompleta = $carpetaDestino . $nombreArchivo;
        rename($nombreArchivo, $rutaCompleta);

        return $rutaCompleta;
    }
}


?>