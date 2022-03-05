<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Database\Factories\TicketFactory;
use Illuminate\Console\Command;

class ProcessTicketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes a ticket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ticket = Ticket::open()->orderBy('created_at', 'asc')->first();

        if($ticket){
            $ticket->status = true;
            $ticket->processed_at = now()->toDateTimeString();
            $ticket->update();

            $this->info('Ticket '.$ticket->id.' has been processed');
        }
        else{
            $this->info('No tickets left to be processed');
        }

    }
}
