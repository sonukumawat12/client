<?php

namespace App\Mail;

use Mpdf\Mpdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
 Illuminate\Queue\SerializesModels;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // Define public data properties to pass to the view

    public function __construct($data)
    {
        $this->data = $data;
        $this->title = $title;
        $this->logo = $logo;
    }

    public function build()
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A5',
            'orientation' => 'L',
            'autoPageBreak' => false,
            'allow_unsafe_image_resizing' => true,

        ]);  
        
        $this->view('sale_pos.receipts.shipping_print_receipt') // Replace 'emails.my_template' with your view name
                    ->with(['data' => $this->data,'title' => $this->title,'logo' => $this->logo])->render(); // Pass data to the view
        $mpdf->WriteHTML($html);
        return $mpdf->Output();
    }
}
