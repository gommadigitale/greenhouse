<?php
// includes/config.php
const DATA_DIR = '/var/www/gommadigitale.com/serra/data/';
const SENSORS = [
    'sensor1' => 'Zona 1',
    'sensor2' => 'Zona 2'
];

date_default_timezone_set('Europe/Rome');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);