<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Public face punch link and verify endpoint
$route['facelogin/facelink/verify']   = 'facelink/verify';
$route['facelogin/facelink/(:any)']   = 'facelink/index/$1';
