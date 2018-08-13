<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
   /** @test */
   function user_can_view_a_concert_listing()
   {

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
                'additional_information' => 'For tickets, call (555) 555-5555',

            ]);

        // Act
        // View the concert listing

            $this->visit('/concert'.$concert->id);
                

        // Assert
        // See the concert details
            $this->see('The Red Chord');
            $this->see('with Animosity and Lethargy');
            $this->see('December 13, 2016');
            $this->see('8:00pm');
            $this->see('32.50');
            $this->see('The Mosh Pit');
            $this->see('123 Example Lane');
            $this->see('Laraville, ON 17916');
            $this->see('For tickets, call (555) 555-5555.');
   }
}
