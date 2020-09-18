<?php


namespace App\Services;


use App\Models\Event;
use App\Models\Schedule;
use App\Models\Vacation;
use App\Repositories\Interfaces\HolidaysRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ScheduleService
{

    /**
     * Объект репозитория праздников
     *
     * @var HolidaysRepository
     */
    protected $holidaysRepository;

    /**
     * Объект модели графика работы
     *
     * @var Schedule
     */
    protected $scheduleModel;

    /**
     * Объект модели отпусков
     *
     * @var Vacation
     */
    protected $vacationModel;

    /**
     * Объект модели событий
     *
     * @var Event
     */
    protected $eventModel;

    public function __construct(HolidaysRepository $holidaysRepository,
                                Schedule $scheduleModel,
                                Vacation $vacationModel,
                                Event $eventModel
    )
    {
        $this->holidaysRepository = $holidaysRepository;
        $this->scheduleModel = $scheduleModel;
        $this->vacationModel = $vacationModel;
        $this->eventModel = $eventModel;
    }

    public function getSchedule(){
        $this->getWorkingDays(1,'2020-01-01','2020-01-30')->forEach(function (Carbon $date) {
            echo $date->format('y-m-d')."\n";});
    }

    /**
     * Возвращает рабочие дни сотрудника
     *
     * @param int $idEmployee
     * @param string $from
     * @param string $to
     * @return CarbonPeriod
     */
    protected function getWorkingDays(int $idEmployee, string $from, string $to): CarbonPeriod
    {
        $holidays = $this->holidaysRepository->getHolidays();
        $vacations = $this->vacationModel->vacationByEmployeeId($idEmployee)->get()->toArray();

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

}
