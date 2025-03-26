<?php

namespace App\Services;

use App\Models\Response;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

class ResponseService
{
    /**
     * Get all responses for a ticket
     *
     * @param int $ticketId
     * @param bool $includeInternal
     * @return Collection
     */
    public function getTicketResponses(int $ticketId, bool $includeInternal = false): Collection
    {
        $query = Response::with('user')
            ->where('ticket_id', $ticketId);
            
        if (!$includeInternal) {
            $query->where('is_internal', false);
        }
        
        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Create a new response
     *
     * @param array $data
     * @return Response
     */
    public function createResponse(array $data): Response
    {
        $response = Response::create($data);
        
        // Update ticket status if needed
        if (!$data['is_internal']) {
            $ticket = Ticket::findOrFail($data['ticket_id']);
            
            // If customer is responding to a resolved ticket, reopen it
            if ($ticket->status === 'resolved' && $response->user->role === 'customer') {
                $ticket->status = 'open';
                $ticket->save();
            }
            
            // If agent is responding to an open ticket, mark it as in progress
            if ($ticket->status === 'open' && $response->user->isAgent()) {
                $ticket->status = 'in_progress';
                $ticket->save();
            }
        }
        
        return $response->fresh();
    }

    /**
     * Update a response
     *
     * @param int $responseId
     * @param array $data
     * @return Response
     */
    public function updateResponse(int $responseId, array $data): Response
    {
        $response = Response::findOrFail($responseId);
        $response->update($data);
        return $response->fresh();
    }

    /**
     * Delete a response
     *
     * @param int $responseId
     * @return bool
     */
    public function deleteResponse(int $responseId): bool
    {
        $response = Response::findOrFail($responseId);
        return $response->delete();
    }
}