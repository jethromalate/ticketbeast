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

    protected function setUp()
    {
        parent::setUp();

        //$this->withoutExceptionHandling();
        
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, $param)
    {
        return $this->json('POST', "/concerts/{$concert->id}/orders",$param);
    }

    private function assertValidationError($return, $field)
    {
        $return->assertStatus(422);
        $this->assertArrayHasKey($field, $return->decodeResponseJson()['errors']);
    }

    /** @test */
    public function customer_can_purchase_tickets_to_a_published_concert()
    {
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $return = $this->orderTickets($concert, [
            'email' => 'jeth@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $return->assertStatus(201);
        
        $order = $concert->orders()->where('email','jeth@example.com')->first();

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test */
    function customer_cannot_purchase_tickets_to_an_unpublished_concert()
    {
        //$this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('unpublished')->create();

        $return = $this->orderTickets($concert, [
            'email' => 'jeth@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        $return->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());

    }

    /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
        //$this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $return = $this->orderTickets($concert, [
            'email' => 'jeth@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        $return->assertStatus(422);

        $order = $concert->orders()->where('email', 'jeth@example.com')->first();
        $this->assertNull($order);
    }

    /** @test */
    function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $return = $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        $this->assertValidationError( $return, 'email');
    }

    /** @test */
    function email_must_be_valid_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $return = $this->orderTickets($concert, [
            'email' => 'non-an-email-address',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        $this->assertValidationError($return, 'email');
    }

    /** @test */
    function ticket_quantity_is_required()
    {
       $concert = factory(Concert::class)->states('published')->create();

       $return = $this->orderTickets($concert, [
           'email' => 'jeth@example.com',
           'payment_token' => $this->paymentGateway->getValidTestToken(),
       ]);
       $this->assertValidationError($return, 'ticket_quantity');
    }

    /** @test */
    function ticket_quantity_must_be_at_least_1_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $return = $this->orderTickets($concert, [
           'email' => 'jeth@example.com',
           'ticket_quantity' => 0,
           'payment_token' => $this->paymentGateway->getValidTestToken(),
       ]);
       $this->assertValidationError($return, 'ticket_quantity');
    }

     /** @test */
    function payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $return = $this->orderTickets($concert, [
            'email' => 'jeth@example.com',
            'ticket_quantity' => 3,
        ]);
        $this->assertValidationError($return, 'payment_token');
    }



}
