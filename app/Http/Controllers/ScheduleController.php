<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScheduleService;
class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public  function show()
    {
        return response()->json($this->scheduleService->getSchedule('2020-01-01', '2020-03-31', 2));
    }
}
