<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!get_option('menus_sections_active')) {
    add_option('menus_sections_active', '[]');
}
