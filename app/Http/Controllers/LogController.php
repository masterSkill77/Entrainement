<?php

namespace App\Http\Controllers;

use App\Models\LogPortique;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use DateTime;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function index()
    {
        $date = '2021-03-01';
        $logs = $this->AllCollaborateurLogs();

        return $logs;
        $data = $logs->getCollection();
        $pauses = $this->calculerToutesPauses($data->all());

        $data = $data->map(function ($personne, $index) use ($pauses) {
            $personne->pause_minutes = $pauses[$index]['Pause_minutes'];
            return $personne;
        });

        dd($data);
    }

    public function AllCollaborateurLogs($date = null)
    {
        $results =  LogPortique::select([
            'Name',
            DB::raw("DATE_FORMAT(`time`, '%Y-%m-%d') as day"),
            DB::raw("GROUP_CONCAT(card_no  SEPARATOR ',') as card"),
            DB::raw("GROUP_CONCAT(DATE_FORMAT(`time`, '%H:%i:%s') ORDER BY `time` SEPARATOR ', ') as heures"),
            DB::raw("MIN(CASE WHEN event_point_name LIKE '%ENTREE' THEN DATE_FORMAT(`time`, '%H:%i:%s') END) as premiere_entree"),
            DB::raw("MAX(CASE WHEN event_point_name LIKE '%SORTIE' THEN DATE_FORMAT(`time`, '%H:%i:%s') END) as derniere_sortie"),
            DB::raw("GROUP_CONCAT(event_point_name ORDER BY `time` SEPARATOR ', ') as events")
        ])
            ->groupBy('Name', 'day')
            ->orderBy('day', 'DESC')
            ->limit(1700)
            ->get()
            ->toArray();

        $newResults = [];
        foreach ($results as $row) {
            $is_night_shift = Carbon::parse($row['premiere_entree'])->format('H:i:s') >= '19:00:00' ||  Carbon::parse($row['derniere_sortie'])->format('H:i:s') <= '04:00:00';

            $events = (explode(',', $row['events']));
            $allEvents = [];
            if (count($events)) {
                $COUNT_ACTION = [
                    'ENTRY' => 0,
                    'EXIT' => 0
                ];

                foreach ($events as $event) {
                    $action = explode('-', $event)[1];
                    $allEvents[] = $action;

                    switch ($action) {
                        case 'ENTREE':
                            $COUNT_ACTION['ENTRY'] += 1;
                            break;
                        case 'SORTIE':
                            $COUNT_ACTION['EXIT'] += 1;
                            break;
                    }
                }

                if (count($allEvents) > 1)
                    $newResults[] = [
                        'is_night_shift' => $is_night_shift,
                        'action' => $COUNT_ACTION,
                        'is_event_unique' => count(array_unique($allEvents)) == 1,
                        ...$row
                    ];
            }
        }

        return $newResults;
        // $haveTwoCards = collect(DB::select("
        // SELECT Name
        // FROM log_portiques
        // GROUP BY Name
        // HAVING COUNT(DISTINCT card_no) > 2
        // "))->pluck("Name");

        // return DB::table('log_portiques')
        //     ->select(
        //         'Name',
        //         DB::raw("GROUP_CONCAT(card_no  SEPARATOR ',') as card"),
        //         DB::raw("DATE_FORMAT(`time`, '%Y-%m-%d') as day"),
        //         DB::raw("MIN(CASE WHEN event_point_name LIKE '%ENTREE' THEN DATE_FORMAT(`time`, '%H:%i:%s') END) as premiere_entree"),
        //         DB::raw("MAX(CASE WHEN event_point_name LIKE '%SORTIE' THEN DATE_FORMAT(`time`, '%H:%i:%s') END) as derniere_sortie"),
        //         DB::raw("GROUP_CONCAT(DATE_FORMAT(`time`, '%H:%i:%s') ORDER BY `time` SEPARATOR ', ') as heures"),
        //         DB::raw("GROUP_CONCAT(event_point_name ORDER BY `time` SEPARATOR ', ') as events")
        //     )
        //     ->when($date, function ($query, $date) {
        //         return $query->whereDate('time', $date);
        //     })
        //     ->whereNotIn('Name', $haveTwoCards)
        //     ->groupBy('Name', DB::raw("DATE_FORMAT(`time`, '%Y-%m-%d')"))
        //     ->orderBy('day', 'ASC')
        //     ->paginate(20);
    }

    public function calculerToutesPauses(array $donnees)
    {
        $resultats = [];

        foreach ($donnees as $personne) {
            // $nom = $personne->Name;
            // $heuresStr = $personne->heures;
            // $eventsStr = $personne->events;

            $totalPause = 0;

            if (!empty($heuresStr) && !empty($eventsStr)) {
                $heuresArray = explode(', ', $heuresStr);
                $eventsArray = explode(', ', $eventsStr);

                var_dump($heuresArray);
                echo '<br/>';
                var_dump($eventsArray);
                echo '<br/>';
                echo '<br/>';
                echo '<br/>';

                for ($i = 0; $i < count($eventsArray) - 1; $i++) {
                    if (strpos($eventsArray[$i], 'SORTIE') !== false && strpos($eventsArray[$i + 1], 'ENTREE') !== false) {
                        $start = new DateTime($heuresArray[$i]);
                        $end = new DateTime($heuresArray[$i + 1]);
                        $interval = $start->diff($end);
                        $minutes = ($interval->h * 60) + $interval->i + ($interval->s / 60);
                        $totalPause += $minutes;
                    }
                }
            }



            $resultats[] = [
                // 'Name' => $nom,
                'Pause_minutes' => round($totalPause, 2),
            ];
        }

        return $resultats;
    }
}
