<?php

namespace Tests\Feature;

use App\Concert;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function customer_can_purchase_tickets()
    {
        //$this->withoutExceptionHandling();

        $paymentGateway = new FakePaymentGateway;

        $this->app->instance(PaymentGateway::class, $paymentGateway);

    // System Flow
    // Arrange - Given
        // Create a concert
        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250
        ]);

    // Act - When
        // Purchase concert tickets
        $return = $this->json('POST', "/concerts/{$concert->id}/orders", [

            'email' => 'jeth@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),

        ]);

     // Assert - Then   
         $return->assertStatus(201);

          
        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $paymentGateway->totalCharges());

         // Make sure than an order exists for this customer
         $order = $concert->orders()->where('email','jeth@example.com')->first();
         $this->assertNotNull($order);
 
         $order = $concert->orders()->where('email','jeth@example.com')->first();
         $this->assertEquals(3, $order->tickets()->count());

       

       
    }
}
