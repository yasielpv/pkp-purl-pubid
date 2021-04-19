<?php

/**
 * @file plugins/pubIds/purl/classes/form/PURLSettingsForm.inc.php
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PURLSettingsForm
 * @ingroup plugins_pubIds_purl
 *
 * @brief Form for journal managers to setup PURL plugin
 */


import('lib.pkp.classes.form.Form');

class PURLSettingsForm extends Form {

	//
	// Private properties
	//
	/** @var integer */
	var $_contextId;

	/**
	 * Get the context ID.
	 * @return integer
	 */
	function _getContextId() {
		return $this->_contextId;
	}

	/** @var PURLPubIdPlugin */
	var $_plugin;

	/**
	 * Get the plugin.
	 * @return PURLPubIdPlugin
	 */
	function _getPlugin() {
		return $this->_plugin;
	}

	//
	// Constructor
	//
	/**
	 * Constructor
	 * @param $plugin PURLPubIdPlugin
	 * @param $contextId integer
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

		$form = $this;
		$this->addCheck(new FormValidatorCustom($this, 'purlObjects', 'required', 'plugins.pubIds.purl.manager.settings.purlObjectsRequired', function($enableIssuePURL) use ($form) {
			return $form->getData('enableIssuePURL') || $form->getData('enableSubmissionPURL') || $form->getData('enableRepresentationPURL');
		}));
		$this->addCheck(new FormValidatorCustom($this, 'purlIssueSuffixPattern', 'required', 'plugins.pubIds.purl.manager.settings.purlIssueSuffixPatternRequired', function($purlIssueSuffixPattern) use ($form) {
			if ($form->getData('purlSuffix') == 'pattern' && $form->getData('enableIssuePURL')) return $purlIssueSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorCustom($this, 'purlSubmissionSuffixPattern', 'required', 'plugins.pubIds.purl.manager.settings.purlSubmissionSuffixPatternRequired', function($purlSubmissionSuffixPattern) use ($form) {
			if ($form->getData('purlSuffix') == 'pattern' && $form->getData('enableSubmissionPURL')) return $purlSubmissionSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorCustom($this, 'purlRepresentationSuffixPattern', 'required', 'plugins.pubIds.purl.manager.settings.purlRepresentationSuffixPatternRequired', function($purlRepresentationSuffixPattern) use ($form) {
			if ($form->getData('purlSuffix') == 'pattern' && $form->getData('enableRepresentationPURL')) return $purlRepresentationSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorUrl($this, 'purlResolver', 'required', 'plugins.pubIds.purl.manager.settings.form.purlResolverRequired'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));

		// for PURL reset requests
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::getRequest();
		$this->setData('clearPubIdsLinkAction', new LinkAction(
			'reassignPURLs',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.purl.manager.settings.purlReassign.confirm'),
				__('common.delete'),
				$request->url(null, null, 'manage', null, array('verb' => 'clearPubIds', 'plugin' => $plugin->getName(), 'category' => 'pubIds')),
				'modal_delete'
			),
			__('plugins.pubIds.purl.manager.settings.purlReassign'),
			'delete'
		));
		$this->setData('pluginName', $plugin->getName());
	}


	//
	// Implement template methods from Form
	//

	/**
	 * @copydoc Form::initData()
	 */
	function initData() {
		$contextId = $this->_getContextId();
		$plugin = $this->_getPlugin();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($contextId, $fieldName));
		}
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array_keys($this->_getFormFields()));
	}

	/**
	 * @copydoc Form::execute()
	 */
	function execute() {
		$contextId = $this->_getContextId();
		$plugin = $this->_getPlugin();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($contextId, $fieldName, $this->getData($fieldName), $fieldType);
		}
	}

	//
	// Private helper methods
	//
	function _getFormFields() {
		return array(
			'enableIssuePURL' => 'bool',
			'enableSubmissionPURL' => 'bool',
			'enableRepresentationPURL' => 'bool',
			'purlPrefix' => 'string',
			'purlSuffix' => 'string',
			'purlIssueSuffixPattern' => 'string',
			'purlSubmissionSuffixPattern' => 'string',
			'purlRepresentationSuffixPattern' => 'string',
			'purlResolver' => 'string',
		);
	}
}


