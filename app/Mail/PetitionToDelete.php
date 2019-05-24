<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;
use App\Status;
use App\Order;

class PetitionToDelete extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    public $leader;
    public $status;
    public $order;
    public $user_asigned;

    public $title;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $leader, Status $status, Order $order)
    {
        $this->url = \Config::get('globals.front_url');
        $this->url = $this->url.'/orders/'. $order->id;

        $this->leader = $leader;
        $this->status = $status;
        $this->order = $order;
        $this->user_asigned = $order->userAssigned()->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = $this->user_asigned->name . ' quiere finalizar una requisición';
        $this->title = $title;

        return $this->markdown('emails.petitionToDelete')->subject($title);
    }
}
