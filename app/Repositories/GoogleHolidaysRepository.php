<?php


namespace App\Repositories;

use Carbon\Carbon;
use GuzzleHttp\Client;

class GoogleHolidaysRepository
{

    const URL = 'https://www.googleapis.com/calendar/v3/calendars/ru.russian%23holiday%40group.v.calendar.google.com/events?key=';

    /**
     * @var Client
     */
    protected $http;

    /**
     * GoogleHolidaysRepository constructor.
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->http = $httpClient;
    }

    /**
     * Возвращает массив нерабочих дней
     *
     * @return array
     * @throws \Carbon\Exceptions\InvalidFormatException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHolidays(): array
    {
        $url = self::URL . config('calendar-api.key');
        $response = $this->http->request('GET', $url);

        $holidays = json_decode($response->getBody()->getContents())->items;
        foreach ($holidays as $day) {
            $holidaysDates[] = Carbon::create($day->start->date);
        }
        return $holidaysDates;
    }
}

