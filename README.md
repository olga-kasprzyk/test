## Ticket App

###Installation

Create ticket table on both the main and test databases

- php artisan config:cache --env=testing
- php artisan migrate
- php artisan config:cache --env=local
- php artisan migrate

The main database is mysql, test database is sqlite (see configs and test.sqlite)

###Testing

To run the tests use either of the following 2 commands

- phpunit
- php artisan test

To run a specific test run:

- phpunit --filter < name of test >

To generate the code coverage report run

- XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text reports/

The full test coverage report can be found in reports, open either index.html or dashboard.html to view

Tests can be found in:

- tests/Feature/TicketTest.php
- tests/Unit/TicketUnitTest.php

Note: if you're seeing issues around the CSRF token run:
- php artisan config:clear

and rerun tests

###Custom Commands

We have 3 custom commands found in app/Console/Commands, these can be run by the following commands:
- php artisan create:ticket (Generates dummy ticket)
- php artisan process:ticket (Processes ticket)
- php artisan read:stats (Outputs stats to screen)

create:ticket and process:ticket have been scheduled in app/Console/Kernel to run at every 1 and every 5 minutes respectively

To run the scheduler, use the command:
- php artisan schedule:work

## Additional Notes:

app\Traits folder is not my code, but something I use to generate uuids instead of the default incremented integer

Ticket Factory seeds the contents of a dummy ticket

It may be more advantageous to use a small pool of users (selected at random) for populating the dummy ticket in order to see multiple tickets submitted by 1 person.
