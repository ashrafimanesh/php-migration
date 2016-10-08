<?php

class update_product2_table_drop_field2 extends Migrate{
    public function up(){
    	return Schema::update('sample_table2',function(SchemaStruct $SchemaStruct){

            //drop fields
            $SchemaStruct->dropFields(array('field2'));
            $SchemaStruct->bigInt('field20')->notNull();
    	},'mysqli');

    }
    public function down(){
        return Schema::update('sample_table2',function(SchemaStruct $SchemaStruct){
            $SchemaStruct->bigInt('field2')->notNull();
            $SchemaStruct->dropFields(array('field20'));
        },'mysqli');
    }
}