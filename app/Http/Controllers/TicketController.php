<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use Illuminate\Http\Request;

use App\Models\Ticket;

use App\Services\TicketService;

use function Livewire\Volt\protect;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with('user')->latest()->paginate(10);    
        return view('admin.tickets', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request)
    {
        $this->ticketService->createTicket($request);
        return redirect()->route('support')->with('success', 'Заявка успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $this->ticketService->removeTicket($ticket);
        return redirect()->back()->with('succsess', 'Заявка успешно удалена и закрыта!');
    }
}
