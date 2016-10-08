<?php
/**
* @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
* @copyright 2016
* create table column attributes such as nullable, autoIncrement,...
*/

class MysqliSchemaField extends SchemaField{
	public $field_name,$sql,$isPrimary=false;
	public function nullAble(){
		$this->sql.=' NULL';
		return $this;
	}
	
	public function notNull(){
		$this->sql.=' NOT NULL';
		return $this;
	}
	
	public function autoIncrement(){
		$this->sql.=' AUTO_INCREMENT';
		return $this;
	}
	

	public function defaultValue($value){
		$this->sql.=" DEFAULT '$value'";
		return $this;
	}
	
}
