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
			<h2>{l s='Your Web Performance Optimization system' mod='eworldaccelerator'}</h2>
			{if $eacc_version != ''}<h4>{l s='is correctly configured' mod='eworldaccelerator'}</h4>
			{else}<h4>{l s='is not configured yet' mod='eworldaccelerator'}</h4>
			{/if}
		</div>
	</div>

	<hr />

	{if $eacc_version != ''}
	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p>eworld Accelerator {$eacc_version} speed up your website, following Google PageSpeed and YSlow recommandations.</p>
				<p>For more information, visit <a href="http://www.eworld-accelerator.com" target="_blank">http://www.eworld-accelerator.com</a> or your <a href="http://customer.eworld-accelerator.com/" target="_blank">eworld Accelerator's account</a>.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				{if $eworldAcceleratorHandler->isThereSystemUpdate()}<p>A <strong>new version</strong> of eworld Accelerator system is available. <a class="btn btn-default" href="{$eworldAcceleratorHandler->getSystemUpdateLink()}" target="_blank">Download new version</a></p><br />{/if}
				<a class="btn btn-default" target="_blank" href="{$eworldAcceleratorHandler->getDashboardURL()}">Dashboard</a>&nbsp;&nbsp;
				<a class="btn btn-default" target="_blank" href="{$eworldAcceleratorHandler->getConfigurationURL()}">Configuration</a>
			</div>
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				{$errors}{$oks}
				<form novalidate="novalidate" action="" method="post" style="float:left;margin:5px 16px 5px 0;">
					<input type="hidden" value="1" name="submitAction">
					<input type="hidden" value="deleteAllCache" name="action">
					<input type="submit" value="Delete All Cache files" class="btn btn-default btn-lg" name="submit">
				</form>

				<form novalidate="novalidate" action="" method="post" style="float:left;margin:5px 16px 5px 0;">
					<input type="hidden" value="1" name="submitAction">
					<input type="hidden" value="gc" name="action">
					<input type="submit" value="Garbage Collector" class="btn btn-default btn-lg" name="gc">
				</form>

				{if $eworldAcceleratorHandler->isCdnActive()}<form novalidate="novalidate" action="" method="post" style="float:left;margin:5px 16px 5px 0;">
					<input type="hidden" value="1" name="submitAction">
					<input type="hidden" value="cdnPurge" name="action">
					<input type="submit" value="Purge CDN files" class="btn btn-default btn-lg" name="purgeCDN">
				</form>{/if}
			</div>
		</div>
	</div>
	{else}
	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p>eworld Accelerator module is not configured yet.</p>
				<p>Please configure it in <i>Modules</i> administration.</p>
			</div>
		</div>
	</div>
	{/if}
</div>
