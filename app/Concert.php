<?php

namespace App;

use App\Ticket;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    //protected from MassAssignment
    protected $guarded = [];

    protected $dates = ['date'];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100 ,2);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($email, $ticketQuantity)
    {
        // Creating the order
        $order = $this->orders()->create(['email'=> $email]);

        // fetch tickets with no order yet
        $tickets = $this->tickets()->take($ticketQuantity)->get();

        foreach ($tickets as $ticket) {

            // pass in the ticket that we want to save and associate with that order
            $order->tickets()->save($ticket);

        }

        return $order;
    }

    public function addTickets($quantity)
    {
        
        foreach (range(1, $quantity) as $i) {

            $this->tickets()->create([]);

        }
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->whereNull('order_id')->count();
    }
}
