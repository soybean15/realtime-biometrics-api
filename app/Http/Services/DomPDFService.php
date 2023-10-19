<?php


namespace App\Http\Services;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DomPDFService{



    protected array $data;
    protected string $view;

    protected static $pdf;
    // public function __construct( array $data, String $view){

    //     $this->data = $data;
    //     $this->view = $view;

    // }


    public static function generate( String $view,array $data){

        // self::$data = $data;
        // self::$view = $view;
        self::$pdf = PDF::loadView($view, ['data' => $data]);

        return new self();

        
    }



    public function stream() {
       
       
       // Load the data into a view
       
       // Generate the PDF
       $pdfData = self::$pdf->stream();


      return $pdfData;

       // Set the response headers for PDF download
       return response($pdfData)
           ->header('Content-Type', 'application/pdf')
           ->header('Content-Disposition', 'attachment; filename="pdf_file.pdf"');
    }    




    public function download() {

        // Generate the PDF
        $pdfData = self::$pdf->download();

        // Set the response headers for PDF download
        return response($pdfData)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="pdf_file.pdf"');
    }
}