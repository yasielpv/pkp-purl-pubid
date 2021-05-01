<?php

/**
 * @file plugins/pubIds/purl/PURLPubIdPlugin.inc.php
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PURLPubIdPlugin
 * @ingroup plugins_pubIds_purl
 *
 * @brief PURL plugin class
 */


import('classes.plugins.PubIdPlugin');

class PURLPubIdPlugin extends PubIdPlugin {

/**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) {
            return $success;
        }
        if ($success && $this->getEnabled($mainContextId)) {
            HookRegistry::register('Publication::getProperties::summaryProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Publication::getProperties::fullProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Publication::getProperties::values', [$this, 'modifyObjectPropertyValues']);
            HookRegistry::register('Publication::validate', [$this, 'validatePublicationPurl']);
            HookRegistry::register('Galley::getProperties::summaryProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Galley::getProperties::fullProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Galley::getProperties::values', [$this, 'modifyObjectPropertyValues']);
            HookRegistry::register('Issue::getProperties::summaryProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Issue::getProperties::fullProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Issue::getProperties::values', [$this, 'modifyObjectPropertyValues']);
            HookRegistry::register('Form::config::before', [$this, 'addPublicationFormFields']);
            HookRegistry::register('Form::config::before', [$this, 'addPublishFormNotice']);
            HookRegistry::register('TemplateManager::display', [$this, 'loadPurlFieldComponent']);
        }
        return $success;
    }
	//
	// Implement template methods from Plugin.
	//
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	public function getDisplayName() {
		return __('plugins.pubIds.purl.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	public function getDescription() {
		return __('plugins.pubIds.purl.description');
	}


	//
	// Implement template methods from PubIdPlugin.
	//
	/**
	 * @copydoc PKPPubIdPlugin::constructPubId()
	 */
	public function constructPubId($pubIdPrefix, $pubIdSuffix, $contextId) {
		$purl = $pubIdPrefix .'/'. $pubIdSuffix;
		return $purl;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdType()
	 */
	public function getPubIdType() {
		return 'purl';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdDisplayType()
	 */
	public function getPubIdDisplayType() {
		return 'PURL';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdFullName()
	 */
	public function getPubIdFullName() {
		return 'Persistent Uniform Resource Locator';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getResolvingURL()
	 */
	public function getResolvingURL($contextId, $pubId) {
		$resolverURL = $this->getSetting($contextId, 'purlResolver');
		return $resolverURL . $pubId;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdMetadataFile()
	 */
	public function getPubIdMetadataFile() {
		return $this->getTemplateResource('purlSuffixEdit.tpl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::addJavaScript()
	 */
	public function addJavaScript($request, $templateMgr) {

	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdAssignFile()
	 */
	public function getPubIdAssignFile() {
		return $this->getTemplateResource('purlAssign.tpl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::instantiateSettingsForm()
	 */
	public function instantiateSettingsForm($contextId) {
		$this->import('classes.form.PURLSettingsForm');
		return new PURLSettingsForm($this, $contextId);
	}

	/**
	 * @copydoc PKPPubIdPlugin::getFormFieldNames()
	 */
	public function getFormFieldNames() {
		return array('purlSuffix');
	}

	/**
	 * @copydoc PKPPubIdPlugin::getAssignFormFieldName()
	 */
	public function getAssignFormFieldName() {
		return 'assignPURL';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPrefixFieldName()
	 */
	public function getPrefixFieldName() {
		return 'purlPrefix';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getSuffixFieldName()
	 */
	public function getSuffixFieldName() {
		return 'purlSuffix';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getLinkActions()
	 */
	public function getLinkActions($pubObject) {
		$linkActions = array();
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::get()->getRequest();
		$userVars = $request->getUserVars();
		$userVars['pubIdPlugIn'] = get_class($this);
		// Clear object pub id
		$linkActions['clearPubIdLinkActionPURL'] = new LinkAction(
			'clearPubId',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.purl.editor.clearObjectsPURL.confirm'),
				__('common.delete'),
				$request->url(null, null, 'clearPubId', null, $userVars),
				'modal_delete'
			),
			__('plugins.pubIds.purl.editor.clearObjectsPURL'),
			'delete',
			__('plugins.pubIds.purl.editor.clearObjectsPURL')
		);

		if (is_a($pubObject, 'Issue')) {
			// Clear issue objects pub ids
			$linkActions['clearIssueObjectsPubIdsLinkActionPURL'] = new LinkAction(
				'clearObjectsPubIds',
				new RemoteActionConfirmationModal(
					$request->getSession(),
					__('plugins.pubIds.purl.editor.clearIssueObjectsPURL.confirm'),
					__('common.delete'),
					$request->url(null, null, 'clearIssueObjectsPubIds', null, $userVars),
					'modal_delete'
				),
				__('plugins.pubIds.purl.editor.clearIssueObjectsPURL'),
				'delete',
				__('plugins.pubIds.purl.editor.clearIssueObjectsPURL')
			);
		}

		return $linkActions;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getSuffixPatternsFieldName()
	 */
	public function getSuffixPatternsFieldNames() {
		return  array(
			'Issue' => 'purlIssueSuffixPattern',
			'Submission' => 'purlPublicationSuffixPattern',
			'Representation' => 'purlRepresentationSuffixPattern',
		);
	}

	/**
	 * @copydoc PKPPubIdPlugin::getDAOFieldNames()
	 */
	public function getDAOFieldNames() {
		return array('pub-id::purl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	public function isObjectTypeEnabled($pubObjectType, $contextId) {
		return (boolean) $this->getSetting($contextId, "enable${pubObjectType}PURL");
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	public function getNotUniqueErrorMsg() {
		return __('plugins.pubIds.purl.editor.purlSuffixCustomIdentifierNotUnique');
	}
/**
     * Add PURL to submission, issue or galley properties
     *
     * @param $hookName string <Object>::getProperties::summaryProperties or
     *  <Object>::getProperties::fullProperties
     * @param $args array [
     * 		@option $props array Existing properties
     * 		@option $object Submission|Issue|Galley
     * 		@option $args array Request args
     * ]
     *
     * @return array
     */
    public function modifyObjectProperties($hookName, $args)
    {
        $props = & $args[0];

        $props[] = 'pub-id::purl';
    }

    /**
     * Add PURL submission, issue or galley values
     *
     * @param $hookName string <Object>::getProperties::values
     * @param $args array [
     * 		@option $values array Key/value store of property values
     * 		@option $object Submission|Issue|Galley
     * 		@option $props array Requested properties
     * 		@option $args array Request args
     * ]
     *
     * @return array
     */
    public function modifyObjectPropertyValues($hookName, $args)
    {
        $values = & $args[0];
        $object = $args[1];
        $props = $args[2];

        // PURLs are already added to property values for Publications and Galleys
        if (get_class($object) === 'IssueGalley' || get_class($object) === 'Publication' || get_class($object) === 'ArticleGalley') {
            return;
        }

        if (in_array('pub-id::purl', $props)) {
            $pubId = $this->getPubId($object);
            $values['pub-id::purl'] = $pubId ? $pubId : null;
        }
    }

    /**
     * Validate a publication's PURL against the plugin's settings
     *
     * @param $hookName string
     * @param $args array
     */
    public function validatePublicationPurl($hookName, $args)
    {
        $errors = & $args[0];
        $action = $args[1];
        $props = & $args[2];

        if (empty($props['pub-id::purl'])) {
            return;
        }

        if ($action === VALIDATE_ACTION_ADD) {
            $submission = Services::get('submission')->get($props['submissionId']);
        } else {
            $publication = Services::get('publication')->get($props['id']);
            $submission = Services::get('submission')->get($publication->getData('submissionId'));
        }

        $contextId = $submission->getData('contextId');
        $purlPrefix = $this->getSetting($contextId, 'purlPrefix');

        $purlErrors = [];
        if (strpos($props['pub-id::purl'], $purlPrefix) !== 0) {
            $purlErrors[] = __('plugins.pubIds.purl.editor.missingPrefix', ['purlPrefix' => $purlPrefix]);
        }
        if (!$this->checkDuplicate($props['pub-id::purl'], 'Publication', $submission->getId(), $contextId)) {
            $purlErrors[] = $this->getNotUniqueErrorMsg();
        }
        if (!empty($purlErrors)) {
            $errors['pub-id::purl'] = $purlErrors;
        }
    }

    /**
     * Add PURL fields to the publication identifiers form
     *
     * @param $hookName string Form::config::before
     * @param $form FormComponent The form object
     */
    public function addPublicationFormFields($hookName, $form)
    {
        if ($form->id !== 'publicationIdentifiers') {
            return;
        }

        if (!$this->getSetting($form->submissionContext->getId(), 'enablePublicationPURL')) {
            return;
        }

        $prefix = $this->getSetting($form->submissionContext->getId(), 'purlPrefix');

        $suffixType = $this->getSetting($form->submissionContext->getId(), 'purlSuffix');
        $pattern = '';
        if ($suffixType === 'default') {
            $pattern = '%j.v%vi%i.%a';
        } elseif ($suffixType === 'pattern') {
            $pattern = $this->getSetting($form->submissionContext->getId(), 'purlPublicationSuffixPattern');
        }

        // If a pattern exists, use a DOI-like field to generate the PURL
        if ($pattern) {
            $fieldData = [
                'label' => __('plugins.pubIds.purl.displayName'),
                'value' => $form->publication->getData('pub-id::purl'),
                'prefix' => $prefix,
                'pattern' => $pattern,
                'contextInitials' => $form->submissionContext->getData('acronym', $form->submissionContext->getData('primaryLocale')) ?? '',
                'submissionId' => $form->publication->getData('submissionId'),
                'assignIdLabel' => __('plugins.pubIds.purl.editor.purl.assignPurl'),
                'clearIdLabel' => __('plugins.pubIds.purl.editor.purl.clearPurl'),
				'separator' => '/',
            ];
            if ($form->publication->getData('pub-id::publisher-id')) {
                $fieldData['publisherId'] = $form->publication->getData('pub-id::publisher-id');
            }
            if ($form->publication->getData('pages')) {
                $fieldData['pages'] = $form->publication->getData('pages');
            }
            if ($form->publication->getData('issueId')) {
                $issue = Services::get('issue')->get($form->publication->getData('issueId'));
                if ($issue) {
                    $fieldData['issueNumber'] = $issue->getNumber() ?? '';
                    $fieldData['issueVolume'] = $issue->getVolume() ?? '';
                    $fieldData['year'] = $issue->getYear() ?? '';
					$fieldData['issueId'] = $form->publication->getData('issueId') ?? '';
                }
            }
            if ($suffixType === 'default') {
                $fieldData['missingPartsLabel'] = __('plugins.pubIds.purl.editor.missingIssue');
            } else {
                $fieldData['missingPartsLabel'] = __('plugins.pubIds.purl.editor.missingParts');
            }

            $form->addField(new \PKP\components\forms\FieldPubId('pub-id::purl', $fieldData));

        // Otherwise add a field for manual entry that includes a button to generate
        // the check number
        } else {
            // Load the checkNumber.js file that is required for this field
            $this->addJavaScript(Application::get()->getRequest(), TemplateManager::getManager(Application::get()->getRequest()));

            $this->import('classes.form.FieldPurl');
            $form->addField(new \Plugins\Generic\PURL\FieldPurl('pub-id::purl', [
                'label' => __('plugins.pubIds.purl.displayName'),
                'description' => __('plugins.pubIds.purl.editor.purl.description', ['prefix' => $prefix]),
                'value' => $form->publication->getData('pub-id::purl'),
            ]));
        }
    }

    /**
     * Show PURL during final publish step
     *
     * @param $hookName string Form::config::before
     * @param $form FormComponent The form object
     */
    public function addPublishFormNotice($hookName, $form)
    {
        if ($form->id !== 'publish' || !empty($form->errors)) {
            return;
        }

        $submission = Services::get('submission')->get($form->publication->getData('submissionId'));
        $publicationPurlEnabled = $this->getSetting($submission->getData('contextId'), 'enablePublicationPURL');
        $galleyPurlEnabled = $this->getSetting($submission->getData('contextId'), 'enableRepresentationPURL');
        $warningIconHtml = '<span class="fa fa-exclamation-triangle pkpIcon--inline"></span>';

        if (!$publicationPurlEnabled && !$galleyPurlEnabled) {
            return;

        // Use a simplified view when only assigning to the publication
        } elseif (!$galleyPurlEnabled) {
            if ($form->publication->getData('pub-id::purl')) {
                $msg = __('plugins.pubIds.purl.editor.preview.publication', ['purl' => $form->publication->getData('pub-id::purl')]);
            } else {
                $msg = '<div class="pkpNotification pkpNotification--warning">' . $warningIconHtml . __('plugins.pubIds.purl.editor.preview.publication.none') . '</div>';
            }
            $form->addField(new \PKP\components\forms\FieldHTML('purl', [
                'description' => $msg,
                'groupId' => 'default',
            ]));
            return;

        // Show a table if more than one PURL is going to be created
        } else {
            $purlTableRows = [];
            if ($publicationPurlEnabled) {
                if ($form->publication->getData('pub-id::purl')) {
                    $purlTableRows[] = [$form->publication->getData('pub-id::purl'), 'Publication'];
                } else {
                    $purlTableRows[] = [$warningIconHtml . __('submission.status.unassigned'), 'Publication'];
                }
            }
            if ($galleyPurlEnabled) {
                foreach ((array) $form->publication->getData('galleys') as $galley) {
                    if ($galley->getStoredPubId('purl')) {
                        $purlTableRows[] = [$galley->getStoredPubId('purl'), __('plugins.pubIds.purl.editor.preview.galleys', ['galleyLabel' => $galley->getGalleyLabel()])];
                    } else {
                        $purlTableRows[] = [$warningIconHtml . __('submission.status.unassigned'),__('plugins.pubIds.purl.editor.preview.galleys', ['galleyLabel' => $galley->getGalleyLabel()])];
                    }
                }
            }
            if (!empty($purlTableRows)) {
                $table = '<table class="pkpTable"><thead><tr>' .
                    '<th>' . __('plugins.pubIds.purl.displayName') . '</th>' .
                    '<th>' . __('plugins.pubIds.purl.editor.preview.objects') . '</th>' .
                    '</tr></thead><tbody>';
                foreach ($purlTableRows as $purlTableRow) {
                    $table .= '<tr><td>' . $purlTableRow[0] . '</td><td>' . $purlTableRow[1] . '</td></tr>';
                }
                $table .= '</tbody></table>';
            }
            $form->addField(new \PKP\components\forms\FieldHTML('purl', [
                'description' => $table,
                'groupId' => 'default',
            ]));
        }
    }

    /**
     * Load the FieldPurl Vue.js component into Vue.js
     *
     * @param string $hookName
     * @param array $args
     */
    public function loadPurlFieldComponent($hookName, $args)
    {
        $templateMgr = $args[0];
        $template = $args[1];

        if ($template !== 'workflow/workflow.tpl') {
            return;
        }

        $templateMgr->addJavaScript(
            'purl-field-component',
            Application::get()->getRequest()->getBaseUrl() . '/' . $this->getPluginPath() . '/js/FieldPurl.js',
            [
                'contexts' => 'backend',
                'priority' => STYLE_SEQUENCE_LAST,
            ]
        );

        $templateMgr->addStyleSheet(
            'purl-field-component',
            '
				.pkpFormField--purl__input {
					display: inline-block;
				}

				.pkpFormField--purl__button {
					margin-left: 0.25rem;
					height: 2.5rem; // Match input height
				}
			',
            [
                'contexts' => 'backend',
                'inline' => true,
                'priority' => STYLE_SEQUENCE_LAST,
            ]
        );
    }
	
}


