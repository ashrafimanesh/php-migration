<?php
/**
* @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
* @copyright 2016
* run migration sqls for create , alter , drop ,... tables
*/
class Schema {
	//call create table
	public static function create($table_name,$function,$driver='mysqli'){
		if(!in_array($driver,array('mysqli'))){
			die('invalid migration driver');
		}
		return self::_call($driver,$table_name,'create',$function);
	}

	//call drop table
	public static function drop($table_name,$driver='mysqli'){
		if(!in_array($driver,array('mysqli'))){
			die('invalid migration driver');
		}
		return self::_call($driver,$table_name,'drop');
	}

	public static function update($table_name,$function,$driver='mysqli'){
		if(!in_array($driver,array('mysqli'))){
			die('invalid migration driver');
		}
		return self::_call($driver,$table_name,'update',$function);
	}

	private static function _call($driver,$table_name,$type,$function=null){
		switch($driver){
			case 'mysqli':
				require_once 'Mysqli/SchemaField.php';
				require_once 'Mysqli/SchemaTable.php';
				$obj=new MysqliSchemaTable($table_name);

				switch ($type) {
					case 'create':
						$function($obj);
						return $obj->create();
					case 'drop':
						return $obj->drop();
					case 'update':
						$function($obj);
						return $obj->update();
					default:
						die('invalid type');
						break;
				}
		}

	}
}



abstract class SchemaStruct{
	protected $table,$fields,$primaryKey=null,$sql;
	public function __construct($table) {
		$this->table=$table;
		$this->sql='';
	}

	abstract function create();
	abstract function drop();
	/**
	* define integer type query strnig
	* @input $field string . column name
	* @input $length integer. integer length
	* @return SchemaStruct
	*/
	abstract public function integer($field,$length=11);
	/**
	* define tinyInt type query strnig
	* @input $field string . column name
	* @input $length integer. tinyInt length
	* @return SchemaStruct
	*/
	abstract public function tinyInt($field,$length=2);
	/**
	* define bigInt type query strnig
	* @input $field string . column name
	* @input $length integer. bigInt length
	* @return SchemaStruct
	*/
	abstract public function bigInt($field,$length=20);
	/**
	* define string type query strnig
	* @input $field string . column name
	* @input $length integer. string length
	* @return SchemaStruct
	*/
	abstract public function string($field,$length=255);
	/**
	* define float type query strnig
	* @input $field string . column name
	* @input $decimal integer. float decimal length
	* @input $points integer. float points length
	* @return SchemaStruct
	*/
	abstract public function float($field,$decimal=6,$points=2);


}


abstract class SchemaField{
	
	public function primaryKey(){
		$this->isPrimary=true;
	}
	
	abstract public function nullAble();
	abstract public function notNull();
	abstract public function defaultValue($value);
}