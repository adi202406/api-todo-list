<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Reminder;
use Illuminate\Http\Request;
use App\Services\ReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReminderResource;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReminderController extends Controller
{
    protected $reminderService;

    public function __construct(ReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        // Untuk nested route /cards/{card}/reminders
        if ($request->route()->hasParameter('card')) {
            $card = $request->route('card');
            Gate::authorize('viewAny', [Reminder::class, $card]);
            
            $reminders = $card->reminders()
                ->with('card')
                ->orderBy('remind_at')
                ->get();
        } else {
            // Untuk standalone route /reminders
            $reminders = Reminder::whereHas('card.users', function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                })
                ->with('card')
                ->orderBy('remind_at')
                ->get();
        }

        return ReminderResource::collection($reminders);
    }

    public function store(StoreReminderRequest $request, Card $card): JsonResponse
    {
        // Gate::authorize('create', [Reminder::class, $card]);

        $reminder = $card->reminders()->create($request->validated());

        return response()->json([
            'message' => 'Reminder created successfully',
            'data' => new ReminderResource($reminder->load('card'))
        ], Response::HTTP_CREATED);
    }

    public function show(Card $card, Reminder $reminder): JsonResponse
    {
        Gate::authorize('view', [$reminder, $card]);

        return response()->json([
            'data' => new ReminderResource($reminder->load('card'))
        ]);
    }

    public function update(UpdateReminderRequest $request, Card $card, Reminder $reminder): JsonResponse
    {
        Gate::authorize('update', [$reminder, $card]);

        $reminder->update($request->validated());

        return response()->json([
            'message' => 'Reminder updated successfully',
            'data' => new ReminderResource($reminder->load('card'))
        ]);
    }

    public function destroy(Card $card, Reminder $reminder): JsonResponse
    {
        Gate::authorize('delete', [$reminder, $card]);

        $reminder->delete();

        return response()->json([
            'message' => 'Reminder deleted successfully',
        ], Response::HTTP_NO_CONTENT);
    }

    public function processDueReminders(): JsonResponse
    {
        // Gate::authorize('process', Reminder::class);

        $count = $this->reminderService->processDueReminders();

        return response()->json([
            'message' => "Processed {$count} reminders",
        ]);
    }
}