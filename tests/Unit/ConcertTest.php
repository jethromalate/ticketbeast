<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_get_formatted_date()
    {
        // 1. Create a concert with known date
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8:00pm')
        ]);
                
        // 2. Retrieve the formatted date
        $date =  $concert->formatted_date;

        
        // 3. Verify the date is formatted as expected
        $this->assertEquals('December 1, 2016',  $date);
    }

    /** @test */
    function can_get_formatted_start_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('17:00:00')
        ]);
                
        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    function can_get_ticket_price_in_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA  = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB  = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $unPublishedConcert = factory(Concert::class)->create(['published_at' => null]);

        $publishedConcerts = Concert::published()->get();
        
        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unPublishedConcert));
    }


    /** @test */
    function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);

        $order = $concert->orderTickets('jeth@example.com', 3);

        $this->assertEquals('jeth@example.com',$order->email);
        $this->assertEquals(3,$order->tickets()->count());
    }
    
    /** @test */
    function can_add_tickets()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());

    }

    /** @test */
    function tickets_remaining_does_not_include_tickets_associated_with_an_order()
    {

        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $concert->orderTickets('jeth@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());


    }

    /** @test */
    function trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(10);

        try {
            
            $concert->orderTickets('jeth@example.com', 11);
            
        } catch (NotEnoughTicketsException $e) {
            
            // make sure no order has been created and lets try and fetch an order
            $order = $concert->orders()->where('email', 'jeth@example.com')->first();
            $this->assertNull($order);

            //check if 10 tickets is still available even if failed
            $this->assertEquals(10, $concert->ticketsRemaining());
            return; 
        }

        $this->fail("Order succeeded even though there were not enough tickets remaining.");
    }

    /** @test */
    function cannot_order_tickets_that_have_already_been_purchase()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(10);

        $concert->orderTickets('jeth@example.com', 8);
        
        try {
            
            $concert->orderTickets('john@example.com', 3);
            
        } catch (NotEnoughTicketsException $e) {
            
            // make sure no order has been created and lets try and fetch an order
            $johnsOrder = $concert->orders()->where('email', 'john@example.com')->first();
            $this->assertNull($johnsOrder);

            //check if 10 tickets is still available even if failed
            $this->assertEquals(2, $concert->ticketsRemaining());
            return; 
        }

        $this->fail("Order succeeded even though there were not enough tickets remaining.");

    }
}
