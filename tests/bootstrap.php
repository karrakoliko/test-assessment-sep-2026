<?php

$_ENV['APP_ENV'] = 'test'; // should be before bootstrap to override existing .env vars with .env.test ones

require __DIR__ . '/../config/bootstrap.php';
