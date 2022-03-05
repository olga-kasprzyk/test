<?php

namespace Tests\Feature;


use App\Models\Ticket;
use Database\Factories\TicketFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;
    //use WithoutMiddleware;

    private array $dummyTicket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dummyTicket = (new TicketFactory)->definition();
    }

    /**
     * Test that a ticket can be added.
     *
     * @return void
     * @test
     */
    public function can_add_ticket()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/tickets', $this->dummyTicket);

        $response->assertStatus(200);
        $this->assertCount(1, Ticket::all());

        $this->assertEquals($this->dummyTicket['subject'], Ticket::first()->subject);
        $this->assertEquals($this->dummyTicket['content'], Ticket::first()->content);
        $this->assertEquals($this->dummyTicket['author'], Ticket::first()->author);
        $this->assertEquals($this->dummyTicket['email'], Ticket::first()->email);
        $this->assertEquals(false, Ticket::first()->status);
        $this->assertEquals(NULL, Ticket::first()->processed_at);
        //$this->seeInDatabase('tickets', ['email' => $this->dummyTicket->email]);
    }

    /**
     * A ticket must contain a subject
     * @test
    */

    public function a_ticket_has_subject(){
        //$this->withoutExceptionHandling();
        $ticket = $this->dummyTicket;
        $ticket['subject'] = '';

        $response = $this->post('/tickets', $ticket);

        $response->assertSessionHasErrors('subject');
    }

    /**
     * A ticket has content
     * @test
     */

    public function a_ticket_has_content(){
        //$this->withoutExceptionHandling();
        $ticket = $this->dummyTicket;
        $ticket['content'] = '';
        $response = $this->post('/tickets', $ticket);

        $response->assertSessionHasErrors('content');
    }

    /**
     * A ticket has author
     * @test
     */

    public function a_ticket_has_author(){
        //$this->withoutExceptionHandling();
        $ticket = $this->dummyTicket;
        $ticket['author'] = '';
        $response = $this->post('/tickets', $ticket);

        $response->assertSessionHasErrors('author');
    }

    /**
     * A ticket has email
     * @test
     */

    public function a_ticket_has_email(){
        //$this->withoutExceptionHandling();
        $ticket = $this->dummyTicket;
        $ticket['email'] = '';
        $response = $this->post('/tickets', $ticket);

        $response->assertSessionHasErrors('email');
    }

    /**
     * A ticket status should default to false
     * @test
     */

    public function a_ticket_has_default_status(){
        //$this->withoutExceptionHandling();
        $response = $this->post('/tickets', $this->dummyTicket);

        $this->assertEquals(false, Ticket::first()->status);
    }

    /**
     * I can process a ticket
     * @test
     */

    public function can_process_ticket(){
        $this->withoutExceptionHandling();
        $response = $this->post('/tickets', $this->dummyTicket);

        //should be false by default
        $this->assertEquals(false, Ticket::first()->status);

        $now = now()->toDateTimeString();

        //I will update the ticket
        $response = $this->patch('/tickets/'.Ticket::first()->id, [
            'status' => true,
            'processed_at' => $now
        ]);

        $response->assertStatus(200);

        //now should change to true
        $this->assertEquals(true, Ticket::first()->status);
        $this->assertEquals($now, Ticket::first()->processed_at);
    }

    /**
     * Get tickets by status
     * @test
     */
    public function get_tickets_by_status(){
        $this->withoutExceptionHandling();
        $now = now()->toDateTimeString();

        //create 4 dummy tickets
        for ($x = 0; $x <= 3; $x++) {
            $this->post('/tickets', (new TicketFactory)->definition());
        }

        //process 1
        $this->patch('/tickets/'.Ticket::first()->id, [
            'status' => true,
            'processed_at' => $now
        ]);

        //get open tickets
        $response = $this->get('/tickets/open');
        $response->assertStatus(200);
        $tickets = $response->json();

        $this->assertCount(3, $tickets['data']);

        //get closed tickets
        $response = $this->get('/tickets/closed');
        $response->assertStatus(200);
        $tickets = $response->json();

        $this->assertCount(1, $tickets['data']);
    }

    /**
     * get tickets by author
     * @test
     */
    public function get_tickets_by_author(){
        $this->withoutExceptionHandling();

        $ticket1 = (new TicketFactory)->definition();
        $ticket2 = (new TicketFactory)->definition();

        $this->post('/tickets', $ticket1);
        $this->post('/tickets', $ticket1);
        $this->post('/tickets', $ticket2);

        //get tickets by user
        $response = $this->get('/users/'.$ticket1['email'].'/tickets');
        $response->assertStatus(200);
        $tickets = $response->json();
        $this->assertCount(2, $tickets['data']);
    }

    /**
     * test to check ticket stats - total count
     * @test
     */
    public function check_ticket_stats_total(){
        $this->withoutExceptionHandling();
        $this->post('/tickets', (new TicketFactory)->definition());
        $this->post('/tickets', (new TicketFactory)->definition());

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect 2
        $this->assertEquals(2, $stats['total']);
    }

    /**
     * test to check ticket stats - unprocessed
     * @test
     */
    public function check_ticket_stats_unprocessed(){
        $this->withoutExceptionHandling();
        $this->post('/tickets', (new TicketFactory)->definition());
        $this->post('/tickets', (new TicketFactory)->definition());

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect 2
        $this->assertEquals(2, $stats['unprocessed']);

        //process 1
        $this->patch('/tickets/'.Ticket::first()->id, [
            'status' => true,
            'processed_at' => now()->toDateTimeString()
        ]);

        //should expect 1
        $response = $this->get('/stats');
        $stats = $response->json();
        $this->assertEquals(1, $stats['unprocessed']);
    }

    /**
     * test to check ticket stats - last processed
     * @test
     */
    public function check_ticket_stats_last_processed(){
        //$this->withoutExceptionHandling();
        $this->post('/tickets', (new TicketFactory)->definition());
        $this->post('/tickets', (new TicketFactory)->definition());

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect Never as none have been processed
        $this->assertEquals('Never', $stats['last_processed']);

        //I will update the ticket
        $now = now()->toDateTimeString();

        $this->patch('/tickets/'.Ticket::first()->id, [
            'status' => true,
            'processed_at' => $now
        ]);

        $response = $this->get('/stats');
        $stats = $response->json();

        //should expect a date as none have been processed
        $this->assertEquals($now, $stats['last_processed']);
    }

    /**
     * get largest author contributor
     * @test
     */
    public function get_largest_contributor(){
        $this->withoutExceptionHandling();

        $ticket1 = (new TicketFactory)->definition();
        $ticket2 = (new TicketFactory)->definition();

        $this->post('/tickets', $ticket1);
        $this->post('/tickets', $ticket1);
        $this->post('/tickets', $ticket2);

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect ticket 1 author
        $this->assertEquals($ticket1['author'], $stats['author']);
    }
}
