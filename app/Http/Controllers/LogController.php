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
    const PER_PAGE = 15;

    public function index(Request $request)
    {
        $date = $request->query('date'); 

        $data = $this->collaboratorLogs($date); 

        return Inertia::render('portique/logs',[
            'log_portiques'=>$data 
        ]);
    }

    public function collaboratorLogs( ?string $date = null)
    {
        $query =  LogPortique::select([
            'Name',
            'pin',
            DB::raw("DATE_FORMAT(`time`, '%Y-%m-%d') as day"),
            DB::raw("GROUP_CONCAT(DISTINCT card_no  SEPARATOR ',') as card"),
            DB::raw("GROUP_CONCAT(DATE_FORMAT(`time`, '%H:%i:%s') ORDER BY `time` SEPARATOR ', ') as heures"),
            DB::raw("MIN(CASE WHEN event_point_name LIKE '%ENTREE' THEN DATE_FORMAT(`time`, '%H:%i:%s') END) as premiere_entree"),
            DB::raw("MAX(CASE WHEN event_point_name LIKE '%SORTIE' THEN DATE_FORMAT(`time`, '%H:%i:%s') END) as derniere_sortie"),
            DB::raw("GROUP_CONCAT(event_point_name ORDER BY `time` SEPARATOR ', ') as events")
        ])
            ->when($date,function($query) use ($date) {
                $query->whereDate('time', $date);
            })
            ->groupBy('Name', 'day', 'pin')
            ->orderBy('day', 'DESC')
            ->paginate(self::PER_PAGE);

        $results = (clone $query)->getCollection()->toArray();

        
        //on parcourt afin de calculer repos et d'avoir premier entree ainsi la derniere sortie
        $newResults = [];
        foreach ($results as $row) {
            $is_night_shift = Carbon::parse($row['premiere_entree'])->format('H:i:s') >= '19:00:00' ||  Carbon::parse($row['derniere_sortie'])->format('H:i:s') <= '04:00:00';
            $hours = explode(',', $row['heures']);
            $events = (explode(',', $row['events']));
            $is_unique_card = count(array_unique(explode(',', $row['card']))) == 1;

            $allEvents = [];
            if (count($events)) {
                $COUNT_ACTION = [
                    'ENTRY' => 0,
                    'EXIT' => 0
                ];

                $lastEvent = null;
                $i = 0;
                $seconds = 0;
                $totalPause = 0;
                foreach ($events as $event) {
                    $action = explode('-', $event)[1] ?? null;
                    if ($lastEvent == 'SORTIE' && $action == 'ENTREE') {
                        $exitHour = new DateTime($hours[$i - 1]);
                        $entryHour = new DateTime($hours[$i]);
                        $interval = $entryHour->diff($exitHour);
                        $seconds += ($interval->h * 3600) + $interval->i * 60 + ($interval->s);
                        $totalPause += 1;
                    }

                    $lastEvent = $action;

                    $allEvents[] = $action;

                    switch ($action) {
                        case 'ENTREE':
                            $COUNT_ACTION['ENTRY'] += 1;
                            break;
                        case 'SORTIE':
                            $COUNT_ACTION['EXIT'] += 1;
                            break;
                    }
                    $i++;
                }

               //En supposant que la valeur max de repos est 1heure
               //le pause inferieur a 1minutes ne sera pas considerer
                if (count($allEvents) > 1)
                    $newResults[] = [
                        'nb_seconds_pauses' => ($seconds < 60 || $seconds > 3600) ? 0 : $seconds,
                        'nb_pause' => $totalPause,
                        'is_unique_card' => $is_unique_card,
                        'is_night_shift' => $is_night_shift,
                        'action' => $COUNT_ACTION,
                        'is_event_unique' => count(array_unique($allEvents)) == 1,
                        ...$row
                    ];
            }
        }

        $query->setCollection(collect($newResults));
        return $query;
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
