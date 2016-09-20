{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
* @author    eworld Accelerator
* @copyright 2010-2016 E-WORLD-CONCEPT SAS
* @license   LICENSE.txt
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
		<li><strong>You know how to configure a cron job</strong> => add this <em>0 5 * * * wget {$smarty.const._PS_BASE_URL_|escape:'htmlall':'UTF-8'}{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/updatealert/cron.php -O /dev/null</em></li>
		<li>or</li>
		<li><strong>You don't know how to add a cron job</strong> => so register on <a href="https://cron-job.org/" target="_blank">cron-job.org (100% free service)</a> and create a cron job like <a href="{$smarty.const._PS_BASE_URL_|escape:'htmlall':'UTF-8'}{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/updatealert/views/img/cronjob.org.png" target="_blank">described here</a> with this URL : {$smarty.const._PS_BASE_URL_|escape:'htmlall':'UTF-8'}{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/updatealert/cron.php</li>
	</ul>
	<br />
	Without this cron job, UpdateAlert module can't send email automatically.
</div>
