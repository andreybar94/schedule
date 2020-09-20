<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScheduleService;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public  function show(Request $request): JsonResponse
    {
        if ($request->startDate > $request->endDate){
            abort(400, 'Start date is later than end date');
        }

        $data = $this->scheduleService->getSchedule($request->startDate, $request->endDate, $request->userId);

        return response()->json($data, 200);
    }
}
