<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 14/09/2016
 * Time: 14:06
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
//include(dirname(__FILE__).'/../../classes/Mail.php');
require 'updatealert.php';

$cron = new UpdateAlertCron(true);