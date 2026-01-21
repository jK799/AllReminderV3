<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        return Reminder::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();
    }

    public function store(StoreReminderRequest $request)
    {
        $data = $request->validated();

        // user_id zawsze z tokena (nie z body)
        $data['user_id'] = $request->user()->id;

        return response()->json(
            Reminder::create($data),
            201
        );
    }

    public function show(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);

        return $reminder;
    }

    public function update(UpdateReminderRequest $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);

        $reminder->update($request->validated());

        return $reminder;
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);

        $reminder->delete();

        return response()->noContent();
    }
}
