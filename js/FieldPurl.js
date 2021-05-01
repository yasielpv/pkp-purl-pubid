/**
 * @defgroup plugins_pubIds_purl_js
 */
/**
 * @file plugins/pubIds/purl/js/FieldPurl.js
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief A Vue.js component for a PURL form field that adds a check number.
 */
var template = pkp.Vue.compile('<div class="pkpFormField pkpFormField--text pkpFormField--purl" :class="classes">' +
'			<form-field-label' +
'				:controlId="controlId"' +
'				:label="label"' +
'				:localeLabel="localeLabel"' +
'				:isRequired="isRequired"' +
'				:requiredLabel="__(\'common.required\')"' +
'				:multilingualLabel="multilingualLabel"' +
'			/>' +
'			<div' +
'				v-if="isPrimaryLocale && description"' +
'				class="pkpFormField__description"' +
'				v-html="description"' +
'				:id="describedByDescriptionId"' +
'			/>' +
'			<div class="pkpFormField__control" :class="controlClasses">' +
'				<input' +
'					class="pkpFormField__input pkpFormField--text__input pkpFormField--purl__input"' +
'					ref="input"' +
'					v-model="currentValue"' +
'					:type="inputType"' +
'					:id="controlId"' +
'					:name="localizedName"' +
'					:aria-describedby="describedByIds"' +
'					:aria-invalid="!!errors.length"' +
'					:required="isRequired"' +
'					:style="inputStyles"' +
'				/>' +
'				<field-error' +
'					v-if="errors.length"' +
'					:id="describedByErrorId"' +
'					:messages="errors"' +
'				/>' +
'				</div>' +
'			</div>' +
'		</div>');

pkp.Vue.component('field-purl', {
	name: 'FieldPurl',
	extends: pkp.Vue.component('field-text'),
	props: {
		purlPrefix: {
			type: String,
			required: true
		}
	},
	methods: {
	},
	render: function(h) {
		return template.render.call(this, h);
	}
});