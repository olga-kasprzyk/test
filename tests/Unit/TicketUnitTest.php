<?php

namespace Tests\Unit;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketUnitTest extends TestCase
{
    use DatabaseMigrations;
    //use RefreshDatabase;

    /**
     * Testing ticket closed scope.
     *
     * @test
     */
    public function ticket_local_scope_open()
    {
        $this->assertInstanceOf(Builder::class, Ticket::open());
        $this->assertTrue(true);
    }

    /**
     * Testing ticket closed scope.
     *
     * @test
     */
    public function ticket_local_scope_cloed()
    {
        $this->assertInstanceOf(Builder::class, Ticket::closed());
        $this->assertTrue(true);
    }

    /**
     * Testing ticket closed scope.
     *
     * @test
     */
    public function ticket_local_scope_email()
    {
        $this->assertInstanceOf(Builder::class, Ticket::email('email'));
        $this->assertTrue(true);
    }

    /** @test */
    public function has_generate_ticket_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\GenerateTicketCommand::class));
    }

    /** @test */
    public function expected_generate_ticket_command_output()
    {
        $this->artisan('create:ticket')
            ->expectsOutput('Dummy Ticket generated')
            ->assertExitCode(0);
    }

    /** @test */
    public function has_process_ticket_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\ProcessTicketCommand::class));
    }

    /** @test */
    public function expected_process_ticket_command_output()
    {
        $this->artisan('process:ticket')
            ->assertExitCode(0);
    }

    /** @test */
    public function has_read_stats_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\ReadStatsCommand::class));
    }

    /** @test */
    public function expected_read_stats_command_output()
    {
        $this->artisan('read:stats')
            ->assertExitCode(0);
    }
}
