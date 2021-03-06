<?php
/**
 * Copyright (c) 2014, Jan Bartel
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this
 *  list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright notice,
 *  this list of conditions and the following disclaimer in the documentation
 *  and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   FormDefaultValues
 * @author    Jan Bartel <barteljan@yahoo.de>
 * @license   BSD
 * @copyright Jan Bartel 2014
 */
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'formDefaultValuesLoadValuesFromDb';
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace('{config_legend}','{default_values_legend},formDefaultValuesLoadValuesFromDb;{config_legend}',$GLOBALS['TL_DCA']['tl_form']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['formDefaultValuesLoadValuesFromDb'] = 'formDefaultValuesTable,formDefaultValuesAlias,formDefaultValuesGetParamName,formDefaultValuesSql';

$GLOBALS['TL_DCA']['tl_form']['fields']['formDefaultValuesLoadValuesFromDb'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_form']['formDefaultValuesLoadValuesFromDb'],
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true,'tl_class'=>'long'),
    'sql'                     => "char(1) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_form']['fields']['formDefaultValuesTable'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_form']['formDefaultValuesTable'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_form', 'getAllTables'),
    'eval'                    => array('submitOnChange'=>true,'chosen'=>true,'tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form']['fields']['formDefaultValuesAlias'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_form']['formDefaultValuesAlias'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'select',
    'options_callback'        => array('jba\form\defaultValues\FormBackendProcessor', 'getAllFields'),
    'eval'                    => array('chosen'=>true,'tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form']['fields']['formDefaultValuesGetParamName'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_form']['formDefaultValuesGetParamName'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>false,'tl_class'=>'clr long'),
    'sql'                     => "varchar(64) NOT NULL default 'id'"
);


$GLOBALS['TL_DCA']['tl_form']['fields']['formDefaultValuesSql'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_form']['formDefaultValuesSql'],
    'exclude'                 => true,
    'filter'                  => false,
    'inputType'               => 'textarea',
    'eval'                    => array('mandatory'=>false, 'rows'=>15, 'allowHTML'=>true, 'tl_class' => 'clr'),
    'sql'                     => "text NULL"
);