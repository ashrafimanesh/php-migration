<?php

/**
* @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
* @copyright 2016
* Mysqli Schema structures
*/
class MysqliSchemaTable extends MysqliFieldsTrait{
	public function __construct($table){
		parent::__construct($table);
	}
	
	/**
	* Return Create Table Query String 
	*/
	public function create(){
		if(sizeof($this->fields)<=0){
			return '';
		}
		
		$sql='CREATE TABLE `'.$this->table.'` (';
		$indexes=',';
		foreach($this->fields as $field){
			$sql.=$field->sql.',';
			if($field->isPrimary){
				$indexes.="PRIMARY KEY (`".$field->field_name."`),";
			}
		}
		
		$sql=rtrim($sql,',');
		$indexes=rtrim($indexes,',');
		$sql.=$indexes.');';
		return $sql;
	}

	public function drop(){

		return 'DROP TABLE `'.$this->table.'`;';
	}

	public function update(){

		$sql='';
		if(sizeof($this->fields)){ //add fields
			foreach($this->fields as $field){
				$sql.=' ADD '.$field->sql.',';
			}
		}
		$sql=trim($this->sql.$sql,',');
		return 'ALTER TABLE `'.$this->table.'` '.$sql;
	}

	public function dropFields($fields=array()){
		foreach($fields as $field){
			$this->sql.="DROP `$field`,";
		}
	}

}


abstract class MysqliFieldsTrait extends SchemaStruct{
	public function __construct($table){
		parent::__construct($table);
		$this->trait='';
	}
	/**
	* define float type query strnig
	* @input $field string . column name
	* @input $decimal integer. float decimal length
	* @input $points integer. float points length
	* @return MysqliSchemaField
	*/
	public function float($field,$decimal=6,$points=2){
		$obj=new MysqliSchemaField();
		$obj->field_name=$field;
		$obj->sql=" `$field` FLOAT($decimal,$points)";
		$this->fields[]=$obj;
		return $obj;
	}
	
	/**
	* define integer type query strnig
	* @input $field string . column name
	* @input $length integer. integer length
	* @return MysqliSchemaField
	*/
	public function integer($field,$length=11){
		$obj=new MysqliSchemaField();
		$obj->field_name=$field;
		$obj->sql=" `$field` INT($length)";
		$this->fields[]=$obj;
		return $obj;
	}
	
	/**
	* define string type query strnig
	* @input $field string . column name
	* @input $length integer. string length
	* @return MysqliSchemaField
	*/
	public function string($field,$length=255){
		$obj=new MysqliSchemaField();
		$obj->field_name=$field;
		$obj->sql=" `$field` VARCHAR($length)";
		$this->fields[]=$obj;
		return $obj;
	}
	
	/**
	* define tinyInt type query strnig
	* @input $field string . column name
	* @input $length integer. tinyInt length
	* @return MysqliSchemaField
	*/
	public function tinyInt($field,$length=2){
		$obj=new MysqliSchemaField();
		$obj->field_name=$field;
		$obj->sql=" `$field` TINYINT($length)";
		$this->fields[]=$obj;
		return $obj;
	}
	
	/**
	* define bigInt type query strnig
	* @input $field string . column name
	* @input $length integer. bigInt length
	* @return MysqliSchemaField
	*/
	public function bigInt($field,$length=20){
		$obj=new MysqliSchemaField();
		$obj->field_name=$field;
		$obj->sql=" `$field` BIGINT($length)";
		$this->fields[]=$obj;
		return $obj;
	}

}