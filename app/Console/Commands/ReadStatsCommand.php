<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReadStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Outputs ticket stats';

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
        $total = Ticket::all()->count();
        $unprocessed = Ticket::open()->count();
        $last_processed = Ticket::closed()->orderBy('processed_at', 'desc')->first();

        $lpv = (isset($last_processed->id)) ? $last_processed->processed_at : 'Never';

        $author = DB::selectOne(DB::raw('select author,  count(1) as `total` from tickets
group by author
order by count(1) desc'));

        $this->info('Total: '.$total);
        $this->info('Unprocessed: '.$unprocessed);
        $this->info('Last Processed: '.$lpv);

        if($author){
            $this->info('Author: '.$author->author);
        }
    }
}
