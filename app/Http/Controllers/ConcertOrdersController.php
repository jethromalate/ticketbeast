<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    function store($concertId)
    {
        $concert = Concert::find($concertId);

        $ticketQuantity = request('ticket_quantity');
        $token = request('payment_token');
        
        $amount = $ticketQuantity * $concert->ticket_price;

        $this->paymentGateway->charge($amount, $token);

        $order = $concert->orders()->create(['email'=> request('email')]);

        foreach (range(1, $ticketQuantity) as $i) {
            $order->tickets()->create([]);
        }

        return response()->json([], 201);
    }
}
