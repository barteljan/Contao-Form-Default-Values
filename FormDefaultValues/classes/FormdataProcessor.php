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

class FormdataProcessor extends \Frontend{

  private $_database;
  protected $queryCache = array();

  public function loadFormField($objWidget,$pid,$options){

    if(  strlen($objWidget->value)==0 &&
         $options &&
         is_array($options) &&
         strlen($options['defaultValueSql'])>0){

         // fetch first data row from cache
         // if cache isn't set fetch it from the database
         $resultRow = null;

         $sql = $options['defaultValueSql'];

         $insertTags = array();

         preg_match("#\{\{[^\)]*\}\}#",$sql,$insertTags);
         $sql = preg_replace("#\{\{[^\)]*\}\}#"," ? ",$sql);

         $replacedTags = array();
         foreach($insertTags as $tag){
           $replacedTags[] = $this->replaceInsertTags($tag);
         }
        
         $database = $this->getDatabase();
         $statement = $database->prepare($sql);
         $result = call_user_func_array ( array($statement,'execute') , $replacedTags);

         if($result->count()>0){
            $resultRow = $result->fetchAssoc();
            $queryCache[$sql] = $resultRow;
         }

         //set default value if one is given by the data row
         if(isset($resultRow[$objWidget->name])){
           $objWidget->value = $resultRow[$objWidget->name];
         }
    }

    if(  strlen($objWidget->value)==0 &&
         strlen($objWidget->defaultValue)>0 ){
       $objWidget->value = $this->replaceInsertTags($objWidget->defaultValue);
    }

    return $objWidget;
  }


  public function getDatabase(){
    if($_database == null){
      $_database = \Database::getInstance();
    }

    return $_database;
  }

  public function setDatabase($database){
    $_database = $database;
  }

}
