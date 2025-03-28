<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    protected $ticketService;

    public function __construct(TicketService $ticketService){
        $this->ticketService = $ticketService;
    }
    /**
     * Display a listing of the resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ticketInfos = $request->validator(
        [
            'user_id' => ['required|' , 'existe:users,id'],
            'title' => ['required' , 'min:6'],
            'description'=>['required','max:255'],
        ]
        );
        return $this->ticketService->createTicket($ticketInfos);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
    public function destroy(string $id)
    {
        //
    }
}
