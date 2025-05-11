<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PortiqueController extends Controller
{
    const PER_PAGE = 20;
    public function __invoke()
    {
        $collaborateurs = DB::table('log_portiques')
        ->select('pin', 'Name', DB::raw("GROUP_CONCAT(DISTINCT card_no ORDER BY card_no SEPARATOR ',') as card"))
        ->groupBy('pin', 'Name')
        ->paginate(10);
        return Inertia::render('portique/index',[
            'log_portiques'=>$collaborateurs
        ]);
    }
}
