<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerStatementPmt implements FromView,WithTitle
{
    
    public $data; 
    

    public function __construct(
        $logo,
        $contact,
        $ledger_details,
        $business_details,
        $for_pdf,
        $location_details,
        $statement_details,
        $statement,
        $reprint,
        $start_date,
        $end_date,
        $reprint_no
    ){
        $this->logo = $logo;
        $this->contact = $contact;
        $this->ledger_details = $ledger_details;
        $this->business_details = $business_details;
        $this->for_pdf = $for_pdf;
        $this->location_details = $location_details;
        $this->statement_details = $statement_details;
        $this->statement = $statement;
        $this->reprint = $reprint;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->reprint_no = $reprint_no;

    }


    public function view(): View
    {  
        return view('customer_statement.export-pmt',[
            'logo' => $this->logo,
            'contact' => $this->contact,
            'ledger_details' => $this->ledger_details,
            'business_details' => $this->business_details,
            'for_pdf' => $this->for_pdf,
            'location_detils' => $this->location_details,
            'statement_details' => $this->statement_details,
            'statement' => $this->statement,
            'reprint' => $this->reprint,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reprint_no' => $this->reprint_no
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'customer-statement-print';
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setEncoding('UTF-8');
            },
        ];
    }

}