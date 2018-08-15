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


        // Charging the customer
        $this->paymentGateway->charge( request('ticket_quantity') * $concert->ticket_price , request('payment_token'));
        // Creating order tickets
        $concert->orderTickets( request('email'), request('ticket_quantity') );

        return response()->json([], 201);
    }
}
