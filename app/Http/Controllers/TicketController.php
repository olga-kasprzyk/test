<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
           'subject' => 'required|max:255',
           'content' => 'required',
           'author' => 'required|max:255',
           'email' => 'required|max:255',
           'status' => ''
        ]);
        Ticket::create($validator->validated());
    }

    public function update(Ticket $ticket, Request $request){
        $validator = Validator::make($request->all(), [
           'status' => 'required|boolean',
           'processed_at' => 'required|date_format:Y-m-d H:i:s'
        ]);

        $ticket->update($validator->validated());
    }

    public function index(Request $request, $status = false){
        $query = Ticket::all();

        //filter by status if present
        if($status){
            $query = $this->getTicketsByStatus($status);
        }

        return $query->paginate(10);
    }

    public function getTicketsByStatus($status){
        if($status == 'open'){
            return Ticket::open();
        }
        else{
            return Ticket::closed();
        }
    }

    public function getTicketsByEmail($email){
        $query = Ticket::email($email);
        return $query->paginate(10);
    }

    public function getStats(){
        $total = Ticket::all()->count();
        $unprocessed = Ticket::open()->count();
        $last_processed = Ticket::closed()->orderBy('processed_at', 'desc')->first();

        $lpv = (isset($last_processed->id)) ? $last_processed->processed_at : 'Never';

        $author = DB::selectOne(DB::raw('select author,  count(1) as `total` from tickets
group by author
order by count(1) desc'));

        return json_encode(['total' => $total, 'unprocessed' => $unprocessed, 'last_processed' => $lpv, 'author' => $author->author]);
    }
}
