<?php
/**
 * @file plugins/pubIds/purl/classes/form/FieldPurl.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class FieldUrn
 * @ingroup classes_controllers_form
 *
 * @brief A field for entering a PURL.
 */

namespace Plugins\Generic\PURL;

use PKP\components\forms\FieldText;

class FieldPurl extends FieldText
{
    /** @copydoc Field::$component */
    public $component = 'field-purl';

    /** @var string The purlPrefix from the purl plugin sttings */
    public $purlPrefix = '';

    /**
     * @copydoc Field::getConfig()
     */
    public function getConfig()
    {
        $config = parent::getConfig();
        $config['purlPrefix'] = $this->purlPrefix;

        return $config;
    }
}
