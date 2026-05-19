<?php

namespace App\Services;

use App\Http\Requests\TicketRequest;
use Illuminate\Support\Facades\Auth;

use App\Models\Ticket;
use GuzzleHttp\Psr7\Request;

class TicketService
{
    public function createTicket(TicketRequest $ticketRequest) {
        return Auth::user()->tickets()->create([
            'name'     => $ticketRequest->name,
            'category' => $ticketRequest->category,
            'content'  => $ticketRequest->content,
        ]);
    }

    public function removeTicket(Ticket $ticket) {
        return $ticket->delete();
    }

}
