<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    function test()
    {
        $paymentGateway = new FakePaymentGateway;

        $this->app->instance(PaymentGateway::class, $paymentGateway);
    }

    function store($concertId)
    {

        $concert = Concert::published()->findOrFail($concertId);
        
        
        // Add Validation Request
        $this->validate(request(), [
            'email'            => ['required', 'email'],
            'ticket_quantity'  => ['required', 'integer', 'min:1'],
            'payment_token'    => ['required'] 
        ]);


        try {
            // Charging the customer
            $this->paymentGateway->charge( request('ticket_quantity') * $concert->ticket_price , request('payment_token'));
            // Creating order tickets
            $concert->orderTickets( request('email'), request('ticket_quantity') );

            return response()->json([], 201);

        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        }

        
    }
}
