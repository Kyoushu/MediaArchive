# Media Archive

## Installation (Ubuntu 13.10)

Make sure you have the required dependencies by running the following command.

    apt-get install ffmpeg

Install the PHP dependencies and configure the installation of Symfony by running the following.

    composer install

If you haven't done so already, run the following command to create the database.

    app/console doctrine:database:create

Update the database schema

    app/console doctrine:schema:update --force

Add media sources [to-do]

    app/console kyoushu:media:add-source

Roll out the production site

    ./production-update

## Recommended Crontab Entries

    */10 * * * *        username        /project_path/app/console kyoushu:media:scan
    */10 * * * *        username        /project_path/app/console kyoushu:media:start-encode-job --auto
    */10 * * * *        username        /project_path/app/console kyoushu:media:auto-queue-encode-job
