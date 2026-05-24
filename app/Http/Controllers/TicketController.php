<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use Illuminate\Http\Request;

use App\Services\AdminService;

use App\Models\Ticket;

use App\Services\TicketService;

use function Livewire\Volt\protect;

class TicketController extends Controller
{
    protected $adminService;

    public function __construct(
        protected TicketService $ticketService, AdminService $adminService
    ) {
         $this->adminService = $adminService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort   = $request->input('sort', 'newest');
        $status = $request->input('status', 'all');

        $query = Ticket::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $query->when($sort === 'oldest', fn($q) => $q->oldest())
              ->when($sort === 'status', fn($q) => $q->orderByRaw("FIELD(status,'pending','replied','closed')"))
              ->when(!in_array($sort, ['oldest','status']), fn($q) => $q->latest());

        $tickets = $query->paginate(20)->withQueryString();

        $counts = [
            'all'     => Ticket::count(),
            'pending' => Ticket::where('status','pending')->count(),
            'replied' => Ticket::where('status','replied')->count(),
            'closed'  => Ticket::where('status','closed')->count(),
        ];

        return view('admin.tickets', compact('tickets', 'search', 'sort', 'status', 'counts'));
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
        return redirect()->back()->with('success', 'Заявка успешно удалена и закрыта!');
    }

    public function reply(Ticket $ticket, Request $request) {
        $this->adminService->reply($ticket, $request->validate([
            'reply' => 'required|string|max:5000',
            'status' => 'required|string|in:pending,replied,closed',
        ]));

        return redirect()->back()->with('success', 'Ответ успешно отправлен!');
    }
}
