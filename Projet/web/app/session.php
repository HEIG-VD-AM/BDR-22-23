<?php

require_once 'autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}