<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Event::query();

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            $events = $query->orderBy('start_date', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($idOrSlug)
    {
        try {
            $event = is_numeric($idOrSlug)
                ? Event::find($idOrSlug)
                : Event::where('slug', $idOrSlug)->first();

            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug',
            'category' => 'required|string',
            'venue' => 'required|string',
            'organizer' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'required|url',
            'banner' => 'required|url',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            if (empty($data['status'])) {
                $data['status'] = 'draft';
            }
            $event = Event::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Event created successfully',
                'data' => $event
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug,' . $id,
            'category' => 'required|string',
            'venue' => 'required|string',
            'organizer' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'required|url',
            'banner' => 'required|url',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $event->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Event updated successfully',
                'data' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::find($id);
            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event not found'
                ], 404);
            }

            $event->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Event deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
