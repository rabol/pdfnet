<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TemplateTestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        // The first step in every application using PDFNet is to initialize the
        // library. The library is usually initialized only once, but calling
        // Initialize() multiple times is also fine.

        \PDFNet::Initialize(config('services.pdfnet.license_key'));
        \PDFNet::SetResourcesPath(storage_path('pdfnet/Resources'));

        $input_path = storage_path('pdfnet') .'/';
        $output_path = storage_path('pdfnet/output') .'/';
        $input_filename = 'SYH_Letter.docx';
        $output_filename = 'SYH_Letter.pdf';

        $json = '
	    {
		    "dest_given_name": "Janice N.",
            "dest_street_address": "187 Duizelstraat",
            "dest_surname": "Symonds",
            "dest_title": "Ms.",
            "land_location": "225 Parc St., Rochelle, QC ",
            "lease_problem": "According to the city records, the lease was initiated in September 2010 and never terminated",
            "logo": { "image_url": "' . $input_path . 'logo_red.png", "width" : 64, "height":  64 },
            "sender_name": "Arnold Smith"
	    }';

        Log::debug($json);
        try {
            // Create a TemplateDocument object from an input office file.
            $template_doc = \Convert::CreateOfficeTemplate($input_path . $input_filename, NULL);

            // Fill the template with data from a JSON string, producing a PDF document.
            $pdfdoc = $template_doc->FillTemplateJson($json);

            // Save the PDF to a file.
            $pdfdoc->Save($output_path.$output_filename, \SDFDoc::e_linearized, NULL);
            // And we're done!
            return view('template-test')->with('output_filename'.$output_filename);
        }
        catch (\Exception|\Error $e)
        {
            Log::debug($e->getMessage());
        }

        return 'Error!';
    }
}
