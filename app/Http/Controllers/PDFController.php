<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
class PDFController extends Controller
{
    //use PDF;


    public function generatePDF() {
        // Retrieve data from the database
        $data = Employee::all();

        // Load the data into a view
        $pdf = PDF::loadView('reports.attendance_card', ['employee' => $data]);

        // Generate the PDF
        $pdfData = $pdf->output();

        // Set the response headers for PDF download
        return response($pdfData)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="pdf_file.pdf"');
    }
}
