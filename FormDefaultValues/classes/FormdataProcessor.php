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
namespace jba\form\defaultValues;

class FormdataProcessor extends \Frontend
{

    private $_database;
    protected $queryCache = array();

    public function loadFormField($objWidget, $pid, $options)
    {
        //check if form should be filled by a database query
        if ($options["formDefaultValuesLoadValuesFromDb"] == "1" &&
            strlen($objWidget->value) == 0 &&
            $options &&
            is_array($options)
        ) {

            $database = $this->getDatabase();
            $result = null;

            // load form values from a user-defined sql statement, if one is given
            if (strlen($options['formDefaultValuesSql']) > 0) {

                // fetch first data row from cache
                // if cache isn't set fetch it from the database
                $resultRow = null;

                $sql = $options['formDefaultValuesSql'];

                $insertTags = array();

                preg_match("#\{\{[^\)]*\}\}#", $sql, $insertTags);
                $sql = preg_replace("#\{\{[^\)]*\}\}#", " ? ", $sql);

                $replacedTags = array();
                foreach ($insertTags as $tag) {
                    $replacedTags[] = $this->replaceInsertTags($tag);
                }

                $database = $this->getDatabase();
                $statement = $database->prepare($sql);
                $result = call_user_func_array(array($statement, 'execute'), $replacedTags);
            } else if(!empty($objWidget->name)){

                $tableName = isset($options['formDefaultValuesTable'])?$options['formDefaultValuesTable']:null;

                $alias     = isset($options['formDefaultValuesAlias'])?$options['formDefaultValuesAlias']:null;

                $value     = (  isset($options['formDefaultValuesGetParamName']) &&
                                isset($_GET[$options['formDefaultValuesGetParamName']])) ?
                                $_GET[$options['formDefaultValuesGetParamName']] :
                                null;
                $tables = $database->listTables();

                $tableFields = array();
                foreach($database->listFields($tableName) as $field){
                    $tableFields[] = $field['name'];
                }

                //if all table and fields exist, and the value is not null then set form field value
                if(!empty($tableName) && in_array($tableName,$tables)
                    && !empty($alias) && in_array($alias,$tableFields)
                    && in_array($objWidget->name,$tableFields)
                    && !empty($value)){

                    $sql = "SELECT ".$objWidget->name." FROM " . $tableName . " WHERE " . $alias . " LIKE ?";

                    $result = $database->prepare($sql)->execute($value);
                }
            }


            if ($result && $result->count() > 0) {
                $resultRow = $result->fetchAssoc();
                $queryCache[$sql] = $resultRow;
            }

            //set value if one is given by the data row
            if ($resultRow && isset($resultRow[$objWidget->name])) {
                $objWidget->value = $resultRow[$objWidget->name];
            }


        }

        if (strlen($objWidget->value) == 0 &&
            strlen($objWidget->defaultValue) > 0
        ) {
            $objWidget->value = $this->replaceInsertTags($objWidget->defaultValue);
        }

        return $objWidget;
    }


    public function getDatabase()
    {
        if ($this->_database == null) {
            $this->_database = \Database::getInstance();
        }

        return $this->_database;
    }

    public function setDatabase($database)
    {
        $this->_database = $database;
    }

}
