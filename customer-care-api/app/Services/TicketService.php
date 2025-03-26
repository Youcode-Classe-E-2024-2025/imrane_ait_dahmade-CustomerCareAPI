<?php

namespace App\Services;

use App\Models\Tickets;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    /**
     * Get all tickets with pagination and filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllTickets(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Tickets::with(['user', 'agent', 'responses']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get tickets for a specific user
     *
     * @param int $userId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserTickets(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Tickets::with(['user', 'agent', 'responses'])
            ->where('user_id', $userId);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get tickets assigned to a specific agent
     *
     * @param int $agentId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAgentTickets(int $agentId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Tickets::with(['user', 'agent', 'responses'])
            ->where('agent_id', $agentId);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get a specific ticket by ID
     *
     * @param int $ticketId
     * @return Tickets
     */
    public function getTicketById(int $ticketId): Tickets
    {
        return Tickets::with(['user', 'agent', 'responses.user'])->findOrFail($ticketId);
    }

    /**
     * Create a new ticket
     *
     * @param array $data
     * @return Tickets
     */
    public function createTicket(array $data): Tickets
    {
        return Tickets::create($data);
    }

    /**
     * Update a ticket
     *
     * @param int $ticketId
     * @param array $data
     * @return Tickets
     */
    public function updateTicket(int $ticketId, array $data): Tickets
    {
        $ticket = Tickets::findOrFail($ticketId);
        $ticket->update($data);
        return $ticket->fresh();
    }

    /**
     * Assign a ticket to an agent
     *
     * @param int $ticketId
     * @param int $agentId
     * @return Tickets
     */
    public function assignTicket(int $ticketId, int $agentId): Tickets
    {
        $ticket = Tickets::findOrFail($ticketId);
        $agent = User::findOrFail($agentId);
        
        // Check if user is an agent
        if (!$agent->isAgent()) {
            throw new \Exception('User is not an agent');
        }
        
        $ticket->agent_id = $agentId;
        $ticket->status = 'in_progress';
        $ticket->save();
        
        return $ticket->fresh();
    }

    /**
     * Change ticket status
     *
     * @param int $ticketId
     * @param string $status
     * @return Tickets
     */
    public function changeStatus(int $ticketId, string $status): Tickets
    {
        $validStatuses = ['open', 'in_progress', 'resolved', 'closed'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Invalid status');
        }
        
        $ticket = Tickets::findOrFail($ticketId);
        $ticket->status = $status;
        $ticket->save();
        
        return $ticket->fresh();
    }

    /**
     * Delete a ticket
     *
     * @param int $ticketId
     * @return bool
     */
    public function deleteTicket(int $ticketId): bool
    {
        $ticket = Tickets::findOrFail($ticketId);
        return $ticket->delete();
    }
}