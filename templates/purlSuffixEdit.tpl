{**
 * plugins/pubIds/purl/templates/purlSuffixEdit.tpl
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Edit custom PURL suffix for an object (issue, submission, file)
 *
 *}
{load_script context="publicIdentifiersForm" scripts=$scripts}

{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{assign var=enableObjectPURL value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`PURL")}
{if $enableObjectPURL}
	{assign var=storedPubId value=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
	{fbvFormArea id="pubIdPURLFormArea" class="border" title="plugins.pubIds.purl.editor.purl"}
		{assign var=formArea value=true}
		{if $pubIdPlugin->getSetting($currentJournal->getId(), 'purlSuffix') == 'customId' || $storedPubId}
			{if empty($storedPubId)} {* edit custom suffix *}
				{fbvFormSection}
					<p class="pkp_help">{translate key="plugins.pubIds.purl.manager.settings.purlSuffix.description"}</p>
					{fbvElement type="text" label="plugins.pubIds.purl.manager.settings.purlPrefix" id="purlPrefix" disabled=true value=$pubIdPlugin->getSetting($currentContext->getId(), 'purlPrefix') size=$fbvStyles.size.SMALL inline=true }
					{fbvElement type="text" label="plugins.pubIds.purl.manager.settings.purlSuffix" id="purlSuffix" value=$purlSuffix size=$fbvStyles.size.MEDIUM inline=true }
				{/fbvFormSection}
				{if $canBeAssigned}
					<p class="pkp_help">{translate key="plugins.pubIds.purl.editor.canBeAssigned"}</p>
					{assign var=templatePath value=$pubIdPlugin->getTemplateResource('purlAssignCheckBox.tpl')}
					{include file=$templatePath pubId=$pubIdPlugin->getPubId($pubObject) pubObjectType=$pubObjectType}
				{else}
					<p class="pkp_help">{translate key="plugins.pubIds.purl.editor.customSuffixMissing"}</p>
				{/if}
			{else} {* stored pub id and clear option *}
				<p>
					{$storedPubId|escape}<br />
					{capture assign=translatedObjectType}{translate key="plugins.pubIds.purl.editor.purlObjectType"|cat:$pubObjectType}{/capture}
					{capture assign=assignedMessage}{translate key="plugins.pubIds.purl.editor.assigned" pubObjectType=$translatedObjectType}{/capture}
					<p class="pkp_help">{$assignedMessage}</p>
					{include file="linkAction/linkAction.tpl" action=$clearPubIdLinkActionPURL contextId="publicIdentifiersForm"}
				</p>
			{/if}
		{else} {* pub id preview *}
			<p>{$pubIdPlugin->getPubId($pubObject)|escape}</p>
			{if $canBeAssigned}
				<p class="pkp_help">{translate key="plugins.pubIds.purl.editor.canBeAssigned"}</p>
				{assign var=templatePath value=$pubIdPlugin->getTemplateResource('purlAssignCheckBox.tpl')}
				{include file=$templatePath pubId=$pubIdPlugin->getPubId($pubObject) pubObjectType=$pubObjectType}
			{else}
				<p class="pkp_help">{translate key="plugins.pubIds.purl.editor.patternNotResolved"}</p>
			{/if}
		{/if}
	{/fbvFormArea}
{/if}
{* issue pub object *}
{if $pubObjectType == 'Issue'}
	{assign var=enablePublicationPURL value=$pubIdPlugin->getSetting($currentContext->getId(), "enablePublicationPURL")}
	{assign var=enableRepresentationPURL value=$pubIdPlugin->getSetting($currentContext->getId(), "enableRepresentationPURL")}
	{if $enablePublicationPURL || $enableRepresentationPURL}
		{if !$formArea}
			{assign var="formAreaTitle" value="plugins.pubIds.purl.editor.purl"}
		{else}
			{assign var="formAreaTitle" value=""}
		{/if}
		{fbvFormArea id="pubIdPURLIssueobjectsFormArea" class="border" title=$formAreaTitle}
			{fbvFormSection list="true" description="plugins.pubIds.purl.editor.clearIssueObjectsPURL.description"}
				{include file="linkAction/linkAction.tpl" action=$clearIssueObjectsPubIdsLinkActionPURL contextId="publicIdentifiersForm"}
			{/fbvFormSection}
		{/fbvFormArea}
	{/if}
{/if}
