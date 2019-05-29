<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\File;

class Requisicion extends Mailable
{
    use Queueable, SerializesModels;

    public $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($record)
    {
        $file = $record->pdf_file;

        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = 'M&M - Requisición';
        $file_path = storage_path() .'/app/'. $this->file->path;

        return $this->markdown('emails/requisicion')->subject($title)
        ->attach($file_path, [
            'as' => 'test.pdf',
            'mime' => 'application/pdf',
        ]);
    }
}
