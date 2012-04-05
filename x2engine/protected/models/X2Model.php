<?php
/*********************************************************************************
 * The X2CRM by X2Engine Inc. is free software. It is released under the terms of 
 * the following BSD License.
 * http://www.opensource.org/licenses/BSD-3-Clause
 * 
 * X2Engine Inc.
 * P.O. Box 66752
 * Scotts Valley, California 95066 USA
 * 
 * Company website: http://www.x2engine.com 
 * Community and support website: http://www.x2community.com 
 * 
 * Copyright © 2011-2012 by X2Engine Inc. www.X2Engine.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright notice, this 
 *   list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this 
 *   list of conditions and the following disclaimer in the documentation and/or 
 *   other materials provided with the distribution.
 * - Neither the name of X2Engine or X2CRM nor the names of its contributors may be 
 *   used to endorse or promote products derived from this software without 
 *   specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 ********************************************************************************/

abstract class X2Model extends CActiveRecord {

	protected static $_fields = null;	// one copy of fields for all instances of this model
	
	protected function queryFields() {
		if(!isset(self::$_fields))	// only look up fields if they haven't already been looked up
			self::$_fields = Fields::model()->findAllByAttributes(array('modelName'=>get_class($this))); //Yii::app()->db->createCommand()->select('*')->from('x2_fields')->where('modelName="'.get_class($this).'"')->queryAll();
	}
	
	/**
	 * Generates validation rules for custom fields
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
	
		$this->queryFields();
			
		$fields = array(
			'required'=>array(),
			'email'=>array(),
			'int'=>array(),
			'date'=>array(),
			'float'=>array(),
			'boolean'=>array(),
			'safe'=>array(),
		);
		
		foreach(self::$_fields as &$_field) {
		
			switch($_field->type) {
				case 'varchar':
				case 'text':
				case 'url':
				case 'currency':
				case 'dropdown':
					$fields['safe'][] = $_field->fieldName;	// these field types have no rules, but still need to be allowed
					break;
				case 'date':
					$fields['int'][] = $_field->fieldName;		// date is actually an int
					break;
				default:
					$fields[ $_field->type ][] = $_field->fieldName;		// otherwise use the type as the validator name
			}
			
			if($_field->required)
				$fields['required'][] = $_field->fieldName;
		}

		return array(
			array( implode( ',', $fields['required']), 'required' ),
			array( implode( ',', $fields['email']), 'email' ),
			array( implode( ',', $fields['int']+$fields['date'] ), 'numerical', 'integerOnly'=>true ),
			array( implode( ',', $fields['float']), 'numerical' ),
			array( implode( ',', $fields['boolean']), 'boolean' ),
			array( implode( ',', $fields['safe']), 'safe' ),
		);
	}
	
	/**
	 * Returns custom attribute values defined in x2_fields
	 * @return array customized attribute labels (name=>label)
	 * @see generateAttributeLabel
	 */
	public function attributeLabels() {
	
		$this->queryFields();
		
		$labels = array();
			
		foreach(self::$_fields as &$_field)
			$labels[ $_field->fieldName ] = Yii::t(strtolower(get_class($this)),$_field->attributeLabel);

		return $labels;
	}

	/**
	 * Returns the text label for the specified attribute.
	 * This method overrides the parent implementation by supporting
	 * returning the label defined in relational object.
	 * In particular, if the attribute name is in the form of "post.author.name",
	 * then this method will derive the label from the "author" relation's "name" attribute.
	 * @param string $attribute the attribute name
	 * @return string the attribute label
	 * @see generateAttributeLabel
	 * @since 1.1.4
	 */
	public function getAttributeLabel($attribute) {
	
		$this->queryFields();
		
		// don't call attributeLabels(), just look in self::$_fields
		foreach(self::$_fields as &$_field) {
			if($_field->fieldName == $attribute)
				return Yii::t(strtolower(get_class($this)),$_field->attributeLabel);
		}
		// original Yii code
		if(strpos($attribute,'.')!==false) {
			$segs=explode('.',$attribute);
			$name=array_pop($segs);
			$model=$this;
			foreach($segs as $seg) {
				$relations=$model->getMetaData()->relations;
				if(isset($relations[$seg]))
					$model=CActiveRecord::model($relations[$seg]->className);
				else
					break;
			}
			return $model->getAttributeLabel($name);
		} else
			return $this->generateAttributeLabel($attribute);
	}
	
	public function getFields() {
		$this->queryFields();
		return self::$_fields;
	}

	/**
	 * Base search function, includes Retrieves a list of models based on the current search/filter conditions.
	 * @param CDbCriteria $criteria the attribute name
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function searchBase($criteria) {

		$this->queryFields();
		
		foreach(self::$_fields as &$field) {
			$fieldName = $field['fieldName'];
			switch($field['type']) {
				case 'boolean':
					$criteria->compare($fieldName,$this->compareBoolean($this->$fieldName), true);
					break;
				case 'link':
					$criteria->compare($fieldName,$this->compareLookup($field['linkType'], $this->$fieldName), true);
					$criteria->compare($fieldName,$this->$fieldName, true, 'OR');
					break;
				case 'assignment':
					$criteria->compare($fieldName,$this->compareAssignment($this->$fieldName), true);
					break;
				default:
					$criteria->compare($fieldName,$this->$fieldName,true);
			}
		}
		
		if(get_class($this) == 'Contacts')
			$criteria->compare('CONCAT(firstName," ",lastName)', $this->name,true);


		return new SmartDataProvider(get_class($this), array(
			'sort'=>array(
				'defaultOrder'=>'lastUpdated DESC',
			),
			'pagination'=>array(
				'pageSize'=>ProfileChild::getResultsPerPage(),
			),
			'criteria'=>$criteria,
		));
	}

	protected function compareLookup($linkType, $value) {
		if(is_null($value) || $value=="") return null;
		
		$linkType = ucfirst($linkType);
		
		if(class_exists($linkType)) {
			$class = new $linkType;
			$tableName = $class->tableName();
			
			if($linkType == 'Contacts')
				$linkIds = Yii::app()->db->createCommand()->select('id')->from($tableName)->where(array('like','CONCAT(firstName," ",lastName)',"%$value%"))->queryColumn();
			else
				$linkIds = Yii::app()->db->createCommand()->select('id')->from($tableName)->where(array('like','name',"%$value%"))->queryColumn();
				
			return empty($linkIds)? -1 : $linkIds;
		}
		return -1;
	}

	protected function compareBoolean($data) {
		return in_array(mb_strtolower(trim($data)),array( 0, 'f', 'false', Yii::t('actions',"No") ))? 0 : 1;		// default to true unless recognized as false
	}
	
	protected function compareAssignment($data) {
		if(is_null($data))
			return null;
		$userNames = Yii::app()->db->createCommand()->select('username')->from('x2_users')->where(array('like','CONCAT(firstName," ",lastName)',"%$data%"))->queryColumn();
		$groupIds = Yii::app()->db->createCommand()->select('id')->from('x2_groups')->where(array('like','name',"%$data%"))->queryColumn();
		
		return (count($groupIds) + count($userNames) == 0)? -1 : $userNames + $groupIds;
	}
}
