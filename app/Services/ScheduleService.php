<?php


namespace App\Services;


use App\Models\TimeRange;
use App\Models\Vacation;
use App\Repositories\Interfaces\HolidaysRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ScheduleService
{
    /**
     * Дата и время начала корпоратива
     */
    const PARTY_START = '2020-01-10 15:00:00';

    /**
     * Дата и время конца корпоратива
     */
    const PARTY_END = '2020-01-11 00:00:00';

    /**
     * Объект репозитория праздников
     *
     * @var HolidaysRepository
     */
    protected $holidaysRepository;

    public function __construct(HolidaysRepository $holidaysRepository)
    {
        $this->holidaysRepository = $holidaysRepository;
    }

    public function getSchedule(string $from,string $to,int $employeeId){
        $workingDays = $this->getWorkingDays($from, $to, $employeeId);
        $timeRanges = TimeRange::getByEmployeeId($employeeId)->get()->toArray();
        $timetable = $this->getTimetable($timeRanges, $workingDays);
        return $this->convertForResponse($timetable);
    }

    /**
     * Возвращает рабочие дни сотрудника
     *
     * @param int $employeeId
     * @param string $from
     * @param string $to
     * @return CarbonPeriod
     */
    protected function getWorkingDays(string $from, string $to, int $employeeId): CarbonPeriod
    {
        $holidays = $this->holidaysRepository->getHolidays();
        $vacations = Vacation::getByEmployeeId($employeeId)->get()->toArray();

        return CarbonPeriod::create($from, $to)->filter(function ($date) use ($holidays, $vacations){
        return  $date->isWeekday() &&
                !$this->isVacationDay($date, $vacations) &&
                !$this->isHoliday($date, $holidays);
        });
    }

    /**
     * Проверяет является ли дата нерабочим днем
     *
     * @param Carbon $date
     * @param array $holidays
     * @return bool
     */
    protected function isHoliday(Carbon $date, array $holidays): bool
    {
        return in_array($date, $holidays);
    }

    /**
     * Проверяет входит ли дата в отпускные дни
     *
     * @param Carbon $date
     * @param array $vacations
     * @return bool
     */
    protected function isVacationDay(Carbon $date, array $vacations): bool
    {
        foreach ($vacations as $vacation){
            $period = CarbonPeriod::create($vacation['from'], $vacation['to']);
            if($period->contains($date)) return true;
        }
        return false;
    }

    protected function getTimetable(array $timeRanges, CarbonPeriod $workingDays)
    {
        foreach ($workingDays as $day){
            foreach ($timeRanges as $range){
                $timetable[] = [
                    'start' => Carbon::create($day->format('Y-m-d') . ' ' . $range['start']),
                    'end' => Carbon::create($day->format('Y-m-d') . ' ' . $range['end'])
                ];
            }
        }

        return $this->removePartyFromTimetable($timetable);
    }

    protected function removePartyFromTimetable(array $timetable)
    {
        foreach ($timetable as $key => $interval){
            $period = CarbonPeriod::create($interval['start'], $interval['end']);
            if (!$period->overlaps(self::PARTY_START, self::PARTY_END)) continue;

            if ($period->contains(self::PARTY_START) && $period->contains(self::PARTY_END)){
                $timetable[] = [
                    'start' => Carbon::create(self::PARTY_END),
                    'end' => $timetable[$key]['end']
                ];

                $timetable[$key]['end'] = Carbon::create(self::PARTY_START);
            }
            elseif ($period->contains(self::PARTY_START) && !$period->contains(self::PARTY_END)){
                $timetable[$key]['end'] = Carbon::create(self::PARTY_START);
            }elseif (!$period->contains(self::PARTY_START) && $period->contains(self::PARTY_END)){
                $timetable[$key]['start'] = Carbon::create(self::PARTY_END);
            }
        }

        return $timetable;
    }

    protected function convertForResponse(array $timetable)
    {
        foreach ($timetable as $interval){
            $item = new \stdClass();
            $item->day = $interval['start']->format('Y-m-d');
            $item->timeRanges[] = [
                'start' => $interval['start']->format('Hi'),
                'end' => $interval['end']->format('Hi')
            ];
            $data['schedule'][] = $item;
        }
        return $data;
    }
}
