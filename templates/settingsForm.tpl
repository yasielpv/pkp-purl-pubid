{**
 * plugins/pubIds/purl/templates/settingsForm.tpl
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * PURL plugin settings
 *
 *}

<div id="description">{translate key="plugins.pubIds.purl.manager.settings.description"}</div>

<script src="{$baseUrl}/plugins/pubIds/purl/js/PURLSettingsFormHandler.js"></script>
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#purlSettingsForm').pkpHandler('$.pkp.plugins.pubIds.purl.js.PURLSettingsFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="purlSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="pubIds" plugin=$pluginName verb="save"}">
	{csrf}
	{include file="common/formErrors.tpl"}
	{fbvFormArea id="purlObjectsFormArea" title="plugins.pubIds.purl.manager.settings.purlObjects"}
		<p class="pkp_help">{translate key="plugins.pubIds.purl.manager.settings.explainPURLs"}</p>
		{fbvFormSection list="true"}
			{fbvElement type="checkbox" label="plugins.pubIds.purl.manager.settings.enableIssuePURL" id="enableIssuePURL" maxlength="40" checked=$enableIssuePURL|compare:true}
			{fbvElement type="checkbox" label="plugins.pubIds.purl.manager.settings.enablePublicationPURL" id="enablePublicationPURL" maxlength="40" checked=$enablePublicationPURL|compare:true}
			{fbvElement type="checkbox" label="plugins.pubIds.purl.manager.settings.enableRepresentationPURL" id="enableRepresentationPURL" maxlength="40" checked=$enableRepresentationPURL|compare:true}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="purlPrefixFormArea" title="plugins.pubIds.purl.manager.settings.purlPrefix"}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.purl.manager.settings.purlPrefix.description"}</p>
			{fbvElement type="text" id="purlPrefix" value=$purlPrefix required="true" label="plugins.pubIds.purl.manager.settings.purlPrefix" maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="purlSuffixFormArea" title="plugins.pubIds.purl.manager.settings.purlSuffix"}
		<p class="pkp_help">{translate key="plugins.pubIds.purl.manager.settings.purlSuffix.description"}</p>
		{fbvFormSection list="true"}
			{if !in_array($purlSuffix, array("pattern", "customId"))}
				{assign var="checked" value=true}
			{else}
				{assign var="checked" value=false}
			{/if}
			{fbvElement type="radio" id="purlSuffixDefault" name="purlSuffix" value="default" label="plugins.pubIds.purl.manager.settings.purlSuffixDefault" checked=$checked}
			<span class="instruct">{translate key="plugins.pubIds.purl.manager.settings.purlSuffixDefault.description"}</span>
		{/fbvFormSection}
		{fbvFormSection list="true"}
			{fbvElement type="radio" id="purlSuffixCustomId" name="purlSuffix" value="customId" label="plugins.pubIds.purl.manager.settings.purlSuffixCustomIdentifier" checked=$purlSuffix|compare:"customId"}
		{/fbvFormSection}
		{fbvFormSection list="true"}
			{fbvElement type="radio" id="purlSuffixPattern" name="purlSuffix" value="pattern" label="plugins.pubIds.purl.manager.settings.purlSuffixPattern" checked=$purlSuffix|compare:"pattern"}
			<p class="pkp_help">{translate key="plugins.pubIds.purl.manager.settings.purlSuffixPattern.example"}</p>
			{fbvElement type="text" label="plugins.pubIds.purl.manager.settings.purlSuffixPattern.issues" id="purlIssueSuffixPattern" value=$purlIssueSuffixPattern maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.pubIds.purl.manager.settings.purlSuffixPattern.submissions" id="purlPublicationSuffixPattern" value=$purlPublicationSuffixPattern maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.pubIds.purl.manager.settings.purlSuffixPattern.representations" id="purlRepresentationSuffixPattern" value=$purlRepresentationSuffixPattern maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="purlResolverFormArea" title="plugins.pubIds.purl.manager.settings.purlResolver"}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.purl.manager.settings.purlResolver.description"}</p>
			{fbvElement type="text" id="purlResolver" value=$purlResolver required="true" label="plugins.pubIds.purl.manager.settings.purlResolver"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="purlReassignFormArea" title="plugins.pubIds.purl.manager.settings.purlReassign"}
		{fbvFormSection}
			<div class="instruct">{translate key="plugins.pubIds.purl.manager.settings.purlReassign.description"}</div>
			{include file="linkAction/linkAction.tpl" action=$clearPubIdsLinkAction contextId="purlSettingsForm"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.save"}
</form>
<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
