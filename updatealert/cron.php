<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    eworld Accelerator
 *  @copyright 2010-2016 E-WORLD-CONCEPT SAS
 *  @license   LICENSE.txt
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
require 'updatealert.php';

$cron = new UpdateAlertCron();

/* At least one character to say "It's ok!" */
echo 1;