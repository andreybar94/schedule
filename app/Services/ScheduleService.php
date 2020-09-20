<?php


namespace App\Services;


use App\Models\TimeRange;
use App\Models\Vacation;
use App\Repositories\GoogleHolidaysRepository;
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
     * @var GoogleHolidaysRepository
     */
    protected $holidaysRepository;

    /**
     * ScheduleService constructor.
     * @param GoogleHolidaysRepository $holidaysRepository
     */
    public function __construct(GoogleHolidaysRepository $holidaysRepository)
    {
        $this->holidaysRepository = $holidaysRepository;
    }

    /**
     * Возвращает расписание сотрудника для ответа
     *
     * @param string $from
     * @param string $to
     * @param int $employeeId
     * @return array
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function getSchedule(string $from, string $to, int $employeeId): array
    {
        $workingDays = $this->getWorkingDays($from, $to, $employeeId);
        $timeRanges = TimeRange::getByEmployeeId($employeeId)->get()->toArray();
        if(empty($timeRanges)){
            abort(404, 'Not found time ranges for employee');
        }
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

    /**
     * Возвращает рабочий график сотрудника
     *
     * @param array $timeRanges
     * @param CarbonPeriod $workingDays
     * @return array
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    protected function getTimetable(array $timeRanges, CarbonPeriod $workingDays): array
    {
        $timetable = array();
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

    /**
     * Удаляет время корпоратива из графика сотрудника
     *
     * @param array $timetable
     * @return array
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    protected function removePartyFromTimetable(array $timetable): array
    {
        foreach ($timetable as $key => $interval){
            $period = CarbonPeriod::create($interval['start'], $interval['end']);
            //Если время рабочего интервала не пересекается с временем корпоратива, проверяем следующий интервал
            if (!$period->overlaps(self::PARTY_START, self::PARTY_END)) continue;
            /*Если время корпоратива входит в интервал рабочего времени, то добавляем новый интервал,от конца
            корпоратива до конца старого интервала, а конец старого интервала теперь будет во время начала корпоратива*/
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

    /**
     * Преобразовывает данные для ответа
     *
     * @param array $timetable
     * @return array
     */
    protected function convertForResponse(array $timetable): array
    {
        $data = array('schedule' => []);
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
