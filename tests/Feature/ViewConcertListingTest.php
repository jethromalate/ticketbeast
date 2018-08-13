<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;


   /** @test */
   function user_can_view_a_concert_listing()
   {
        $this->withoutExceptionHandling();

       #Arg-Act-Ass - MVP
        // Arrange
        // Create a concert
            # Direct Model Access - your test code has a direct access to the domain code, objects that you need directly
            # instead of having a UI, its faster, also removing some duplication to our test.
            # Design Direction thru - ORM Laravel Eloquent

            $concert = Concert::create([

                'title'=> 'The Red Chord',
                'subtitle' => 'with Animosity and Lethargy',
                'date' => Carbon::parse('December 13, 2016 8:00pm'),
                'ticket_price' => '3250',
                'venue' => 'The Mosh Pit',
                'venue_address' => '123 Example Lane',
                'city' => 'Laraville',
                'state' => 'ON',
                'zip' => '17916',
                'additional_information' => 'For tickets, call (555) 555-5555.',

            ]);

        // Act
        // View the concert listing

            $response = $this->get('/concerts/'.$concert->id);
                

        // Assert
        // See the concert details
            $response->assertStatus(200);
            $response->assertSee('The Red Chord');
            $response->assertSee('with Animosity and Lethargy');
            $response->assertSee('December 13, 2016');
            $response->assertSee('8:00pm');
            $response->assertSee('32.50');
            $response->assertSee('The Mosh Pit');
            $response->assertSee('123 Example Lane');
            $response->assertSee('Laraville, ON 17916');
            $response->assertSee('For tickets, call (555) 555-5555.');
   }


   
}
