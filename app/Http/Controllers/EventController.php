<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\RosterParser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    // Get all events
    public function index()
    {
        return Event::all();
    }

    // Get events between specific dates
    public function getByDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $events = Event::whereBetween('date', [
            Carbon::parse($request->start_date)->toDateString(),
            Carbon::parse($request->end_date)->toDateString(),
        ])->get();

        return response()->json($events);
    }

    // Get flights for the next week
    public function getFlightsForNextWeek()
    {
        $dt = Carbon::create(2022, 1, 14, 0); //mentioned in the assignment doc
        $startDate =  $dt->toDateString();
        $endDate = $dt->addWeek()->toDateString();

        $flights = Event::where('type', 'FLT')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return response()->json($flights);
    }

    // Get standby events for the next week
    public function getStandbyForNextWeek()
    {
        $dt = Carbon::create(2022, 1, 14, 0); //mentioned in the assignment doc
        $startDate =  $dt->toDateString();
        $endDate = $dt->addWeek()->toDateString();

        $standbyEvents = Event::where('type', 'SBY')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return response()->json($standbyEvents);
    }

    // Get flights by start location
    public function getFlightsByLocation(Request $request)
    {
        $request->validate([
            'start_location' => 'required|string',
        ]);

        $flights = Event::where('type', 'FLT')
            ->where('start_location', strtolower($request->start_location))
            ->get();

        return response()->json($flights);
    }

    // Upload roster file and process it
    public function upload(Request $request, RosterParser $rosterParser)
    {
        $request->validate([
            'roster_file' => 'required|file|mimes:html|max:2048',
        ]);
        $filePath = $request->file('roster_file')->getPathname();
        $extension = $request->file('roster_file')->getClientOriginalExtension();
        try {
            $events = $rosterParser->parse($filePath, $extension);
            foreach ($events as $event) {
                Event::updateOrCreate($event);
            }
            
            return response()->json(['message' => 'Roster file processed successfully!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    
    }
}
