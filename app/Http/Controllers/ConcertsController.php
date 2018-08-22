<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;
use App\Billing\FakePaymentGateway;

class ConcertsController extends Controller
{
    public function __construct()
    {
        $paymentGateway = new FakePaymentGateway;
    }

    public function show($id)
    {
        $concert = Concert::published()->findOrFail($id); 

        return view('concerts.show',
            [ 'concert' => $concert ]
        );
    }
}
