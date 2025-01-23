<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching all events.
     */
    public function test_fetch_all_events()
    {
        Event::factory()->count(5)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    /**
     * Test fetching events between dates.
     */
    public function test_fetch_events_by_date_range()
    {
        $startDate = '2022-01-14';
        $endDate = '2022-01-20';

        Event::factory()->create(['date' => '2022-01-15']);
        Event::factory()->create(['date' => '2022-01-19']);
        Event::factory()->create(['date' => '2022-01-21']); // Should not be included

        $response = $this->getJson('/api/events/date-range?start_date=' . $startDate . '&end_date=' . $endDate);

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }


    /**
     * Test fetching flights for the next week.
     */
    public function test_fetch_flights_for_next_week()
    {
        Event::factory()->create([
            'type' => 'FLT',
            'date' => '2022-01-16',
        ]);

        Event::factory()->create([
            'type' => 'FLT',
            'date' => '2022-01-20',
        ]);

        Event::factory()->create([
            'type' => 'FLT',
            'date' => '2022-01-22', // Should not be included
        ]);

        $response = $this->getJson('/api/events/flights/next-week');

        $response->assertStatus(200)
                ->assertJsonCount(2)
                ->assertJsonFragment(['type' => 'FLT']);
    }


    /**
     * Test fetching standby duties for the next week.
     */
    public function test_fetch_standby_for_next_week()
    {
        Event::factory()->create([
            'type' => 'SBY',
            'date' => '2022-01-15',
        ]);

        Event::factory()->create([
            'type' => 'SBY',
            'date' => '2022-01-18',
        ]);

        Event::factory()->create([
            'type' => 'SBY',
            'date' => '2022-01-23', // Should not be included
        ]);

        $response = $this->getJson('/api/events/standby/next-week');

        $response->assertStatus(200)
                ->assertJsonCount(2)
                ->assertJsonFragment(['type' => 'SBY']);
    }


    /**
     * Test fetching flights by location.
     */
    public function test_fetch_flights_by_location()
    {
        Event::factory()->create([
            'type' => 'FLT',
            'start_location' => 'krp',
        ]);

        Event::factory()->create([
            'type' => 'FLT',
            'start_location' => 'cph', // Should not be included
        ]);

        $response = $this->getJson('/api/events/location?start_location=KRP'); // Query parameter

        $response->assertStatus(200)
                ->assertJsonCount(1) // Only 1 record should match
                ->assertJsonFragment(['start_location' => 'krp']);
    }



    /**
     * Test uploading a roster file.
     */
    public function test_upload_roster_file()
    {
        $fileContent = '<html><body><table id="ctl00_Main_activityGrid"><tr><td>Sample</td></tr></table></body></html>';
        $file = UploadedFile::fake()->createWithContent('roster.html', $fileContent);

        $response = $this->postJson('/api/events/upload', [
            'roster_file' => $file,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Roster file processed successfully!']);
    }
}
