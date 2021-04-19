{**
 * @file plugins/pubIds/purl/templates/purlAssign.tpl
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Assign PURL to an object option.
 *}

{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{assign var=enableObjectPURL value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`PURL")}
{if $enableObjectPURL}
	{fbvFormArea id="pubIdPURLFormArea" class="border" title="plugins.pubIds.purl.editor.purl"}
	{if $pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.purl.editor.assignPURL.assigned" pubId=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}</p>
		{/fbvFormSection}
	{else}
		{assign var=pubId value=$pubIdPlugin->getPubId($pubObject)}
		{if !$canBeAssigned}
			{fbvFormSection}
				{if !$pubId}
					<p class="pkp_help">{translate key="plugins.pubIds.purl.editor.assignPURL.emptySuffix"}</p>
				{else}
					<p class="pkp_help">{translate key="plugins.pubIds.purl.editor.assignPURL.pattern" pubId=$pubId}</p>
				{/if}
			{/fbvFormSection}
		{else}
			{assign var=templatePath value=$pubIdPlugin->getTemplateResource('purlAssignCheckBox.tpl')}
			{include file=$templatePath pubId=$pubId pubObjectType=$pubObjectType}
		{/if}
	{/if}
	{/fbvFormArea}
{/if}
