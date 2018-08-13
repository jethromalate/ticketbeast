<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_get_formatted_date()
    {
        $this->withoutExceptionHandling();

        // Create a concert with known date
        $concert = factory(Concert::class)->create([
            'date' => Carbon::parse('2016-12-01 8:00pm')

        ]);
                
        // Retrieve the formatted date
        $date = $concert->formatted_date;

        
        // Verify the date is formatted as expected
        $this->assertEquals('December 1, 2016', $date);
    }
}
