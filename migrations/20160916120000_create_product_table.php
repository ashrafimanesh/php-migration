<?php

class create_product_table extends Migrate{
    public function up(){
    	return Schema::create('sample_table',function(SchemaStruct $SchemaStruct){
    		$SchemaStruct->integer('id')->autoIncrement()->primaryKey();
    		$SchemaStruct->tinyInt('field1')->nullAble()->defaultValue(2);
    		$SchemaStruct->bigInt('field2')->notNull();
    		$SchemaStruct->string('field3');
    		$SchemaStruct->float('field4',10,2);
    	},'mysqli');

    }
    public function down(){
    	return Schema::drop('sample_table','mysqli');
    }
}