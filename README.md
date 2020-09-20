## Schedule
Web service that calculates the work schedule of employees.
## Installation Guide:

    git https://github.com/andreybar94/schedule.git
    cd schedule/
    composer install

- Copy __.env.example__ file to __.env__
- Create database
- Edit database credentials in __.env__
- Add Google Calendar API key in __.env__ CALENDAR_API_KEY

```bash
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
```
- DocumentRoot direct to public/
- Go to http://localhost/schedule?startDate=2020-01-01&?endDate=2020-01-14&userId=1
