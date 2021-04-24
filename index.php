<?php

/**
 * @defgroup plugins_pubIds_purl PURL Pub ID Plugin
 */

/**
 * @file plugins/pubIds/purl/index.php
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_pubIds_purl
 * @brief Wrapper for purl plugin.
 *
 */
require_once('PURLPubIdPlugin.inc.php');

return new PURLPubIdPlugin();


