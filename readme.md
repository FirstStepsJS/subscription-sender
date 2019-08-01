# Article subscription sender

Send scheduled article emails

uses [Laravel](https://laravel.com/) and laravel-mail-api [dotmailer API wrapper](https://github.com/FirstStepsJS/Laravel-mail-api)

Designed to work within a microservice architecture, the articles to be served from a node content api, call found in 
daily & weekly article artisan commands.

## Artisan Commands
 
`send:daily` sends the daily article email. will dynamically determine (per member) whether each members should receive
which articles, `send:weekly` does the same with a weekly schedule.

## Configuration

The dotmailer section in `config/services.php` contains necessary configuration - the API base URL, authentication 
token and a default email address to receive article emails, if `APP_ENV` is not *production*

There is a migration available to create the database table for subscription users if you don't wish to repurpose this model
for your own specific use case.

On the dotdigital portal interface you need to configure your email templates with the mapped personalisation values as follows
@PARAM_NAME_HERE@

Dotmailer does not currently support dynamic defaults with triggered campaigns so there is currently a hard coded array of 
placeholders, customise as you see fit for your use case.

On the server you choose to host the application within specify a cron job as below in your cron tab to trigger kernel schedule.

`* * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1`