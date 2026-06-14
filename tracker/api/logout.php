<?php
require 'config.php';
session_destroy();
jsonResponse(['message' => 'Logged out']);