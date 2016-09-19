{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.png" style="margin-top:16px;" />
		</div>
		<div class="col-xs-7 text-left">
			<h2>{l s='Update Alert system' mod='updatealert'}</h2>
			<h4>{l s='is correctly installed on your Prestashop' mod='updatealert'}</h4>
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p><strong>UpdateAlert</strong> detects if Prestashop or its module is outdated and send emails to every contact below.</p>
			</div>
		</div>
	</div>
</div>

<div class="alert alert-warning">
	<button data-dismiss="alert" class="close" type="button">×</button>
	<h4>UpdateAlert needs a cron job executed every day</h4>
	<ul class="list-unstyled">
		<li><strong>You know how to configure a cron job</strong> => add this <em>0 5 * * * wget {$smarty.const._PS_BASE_URL_|escape:'htmlall':'UTF-8'}/modules/updatealert/cron.php -O /dev/null</em></li>
		<li>or</li>
		<li><strong>You don't know how to add a cron job</strong> => so register on <a href="https://cron-job.org/" target="_blank">cron-job.org (100% free service)</a> and create a cron job like <a href="{$smarty.const._PS_BASE_URL_|escape:'htmlall':'UTF-8'}/modules/updatealert/views/img/cronjob.org.png" target="_blank">described here</a> with this URL : {$smarty.const._PS_BASE_URL_|escape:'htmlall':'UTF-8'}/modules/updatealert/cron.php</li>
	</ul>
	<br />
	Without this cron job, UpdateAlert module can't send email automatically.
</div>
