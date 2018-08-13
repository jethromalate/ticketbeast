<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConcertTest extends TestCase
{
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
    
    
}
