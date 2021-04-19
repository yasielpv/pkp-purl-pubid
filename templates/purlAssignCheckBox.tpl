{**
 * @file plugins/pubIds/purl/templates/purlAssignCheckBox.tpl
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Displayed only if the PURL can be assigned.
 * Assign PURL form check box included in purlSuffixEdit.tpl and purlAssign.tpl.
 *}

{capture assign=translatedObjectType}{translate key="plugins.pubIds.purl.editor.purlObjectType"|cat:$pubObjectType}{/capture}
{capture assign=assignCheckboxLabel}{translate key="plugins.pubIds.purl.editor.assignPURL" pubId=$pubId pubObjectType=$translatedObjectType}{/capture}
{fbvFormSection list=true}
	{fbvElement type="checkbox" id="assignPURL" checked="true" value="1" label=$assignCheckboxLabel translate=false disabled=$disabled}
{/fbvFormSection}
