<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return Event::all();


        Gate::authorize('viewAny', Event::class);
        $query = Event::query();
        $relations = ['user', 'attendees', 'attendees.user'];
        foreach ($relations as $relation) {
            $query->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $q->with($relation)
            );
        }



        return EventResource::collection($query->latest()->paginate());
    }

    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query('include');
        if (!$include) {
            return false;
        }
        $relations = array_map('trim', explode(',', $include));
        return in_array($relation, $relations);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Gate::authorize('create', Event::class);
        $event = Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',

            ]),
            'user_id' => $request->user()->id

        ]);
        // return $event;
        return new EventResource($event);
    }
    // public function store(Request $request)
    // {
    //     // Validate the incoming request data
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'start_time' => 'required|date',
    //         'end_time' => 'required|date|after:start_time',
    //     ]);

    //     // Add user_id to the validated data
    //     $validated['user_id'] = 1;

    //     // Create the event using the validated data
    //     $event = Event::create($validated);

    //     // Return the created event
    //     return $event;
    // }


    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //to return a single event
        // return $event;

        //using eventresource.php
        Gate::authorize('view', $event);
        $event->load('user', 'attendees');
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

        // if (Gate::denies('update-event', $event)) {
        //     abort(403, 'You are not authorized to update this event');
        // }
        Gate::authorize('update', $event);
        $event->update(
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time',

            ])
        );
        // return $event;
        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);
        $event->delete();
        return response()->json([
            'message' => 'event deleted successfully'
        ]);
    }
}
