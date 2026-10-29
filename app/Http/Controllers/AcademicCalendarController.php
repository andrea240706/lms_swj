<?php

namespace App\Http\Controllers;

use App\Services\AcademicCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function __construct(
        public AcademicCalendarService $calendarService
    ) {}

    /**
     * Get academic calendar events & current weekly schedule days.
     */
    public function index(Request $request): JsonResponse
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        $dateParam = $request->query('date');
        $referenceDate = $dateParam ? Carbon::parse($dateParam)->toDateString() : Carbon::now()->toDateString();

        $weekDays = $this->calendarService->getWeekDays($referenceDate);
        $events = $this->calendarService->getCombinedEvents($year);
        $holidays = $this->calendarService->getHolidays($year);

        return response()->json([
            'success' => true,
            'year' => $year,
            'currentDate' => $referenceDate,
            'weekDays' => $weekDays,
            'events' => $events,
            'holidaysCount' => count($holidays),
            'academicAgendaCount' => count($this->calendarService->getAcademicAgenda($year)),
        ]);
    }
}
