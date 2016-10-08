<?php

abstract class Migrate
{
    abstract public function up();
    abstract public function down();
}

class Migration
{
    private $DBNAME='',$DBUSERNAME='root',$DBPASSWORD='',$DBADDRESS='localhost';
    private $db_link;
    public $TABLE='migrations',$MIGRATIONS_DIR='migrations',$SCHEMA_DIR='Schemas';
    public function __construct($DBNAME='',$DBUSERNAME='root',$DBPASSWORD='',$DBADDRESS='localhost')
    {
        $this->DBNAME=$DBNAME;
        $this->DBUSERNAME=$DBUSERNAME;
        $this->DBPASSWORD=$DBPASSWORD;
        $this->DBADDRESS=$DBADDRESS;
    }

    public function up(){

        //get list of migrations file.
        $migration_files=dirToArray($this->MIGRATIONS_DIR);
        if(!sizeof($migration_files)){
            die('migration does not exist');
        }
        //connect to database
        $this->_db();

        //check MIGRATION_TABLE_NAME
        $this->_check_table();

        //check runned migrations 
        $migration_files=$this->_check_runned_migrations($migration_files);

        if(sizeof($migration_files)<=0){
            die('migration does not exist');
        }

        //Load Schemas
        require_once $this->SCHEMA_DIR.'/Schema.php';

        foreach($migration_files as $file){
            $this->_run($file,'up');
        }
    }

    public function down($file=''){
        //connect to database
        $this->_db();
        //check MIGRATION_TABLE_NAME
        $table_found=$this->_check_table(false);

        if(!$table_found){
            die('migration does not exist on '.$this->TABLE.' table');
        }

        $before_migrations=mysqli_query($this->db_link,"SELECT * FROM `migrations`");
        if($before_migrations){

            //Load Schemas
            require_once $this->SCHEMA_DIR.'/Schema.php';

            while($db = mysqli_fetch_assoc($before_migrations)) {
                if(!$file || $file.'.php'==$db['migrate']){
                    $this->_run($db['migrate'],'down');
                }
            }
        }
    }

    /**
    * call migration file up or down method
    * @input $file string. migration file name
    * @input $type string. migrate type (up or down)
    */
    private function _run($file,$type='up'){
        require trim($this->MIGRATIONS_DIR,'/')."/".$file;
        $x=explode('_',$file);
        $date=$x[0];
        unset($x[0]);
        $class=rtrim(implode('_',$x),'.php');
        $class=new $class();
        if($class instanceof Migrate){
            switch ($type) {
                case 'down':
                    $query_string=$class->down();
                    if($query_string){
                        $res=mysqli_query($this->db_link,$query_string) or die(mysqli_error($this->db_link));
                        if($res){
                            $res=mysqli_query($this->db_link,'DELETE FROM '.$this->TABLE." WHERE migrate= '$file' ") or die(mysqli_error($this->db_link));
                            echo '<b style="color:green">drop migration: '.$file."<b/><br/>";
                        }
                    }
                    break;
                default:
                    $query_string=$class->up();
                    if($query_string){
                        $res=mysqli_query($this->db_link,$query_string) or die(mysqli_error($this->db_link));
                        if($res){
                            $res=mysqli_query($this->db_link,'INSERT INTO '.$this->TABLE." VALUES ('$file')") or die(mysqli_error($this->db_link));
                            echo '<b style="color:green">run: '.$file."<b/><br/>";
                        }
                    }
                    break;
            }
        }
    }

    private function _db(){
        $this->db_link = mysqli_connect($this->DBADDRESS, $this->DBUSERNAME, $this->DBPASSWORD,$this->DBNAME);
        if (!$this->db_link) {
            echo "Failed to connect to the database.\n";
            exit;
        }
        mysqli_query($this->db_link,"SET NAMES 'utf8'");
    }

    private function _check_table($create=true){
        $tables=mysqli_query($this->db_link,"SHOW TABLES");
        $table_found=false;
        if($tables){
            while($table = mysqli_fetch_assoc($tables)) {
                if($table['Tables_in_pmcms']==$this->TABLE){
                    $table_found=true;
                    break;
                }
            }
        }

        if(!$table_found && $create){
            mysqli_query($this->db_link,"CREATE TABLE `".$this->TABLE."` (`migrate` VARCHAR( 255 ) NOT NULL, PRIMARY KEY (  `migrate` )) ENGINE = MYISAM") or die(mysqli_error($this->db_link));
        }
        return $table_found;
    }

    private function _check_runned_migrations($migration_files){
        $before_migrations=mysqli_query($this->db_link,"SELECT * FROM `migrations`");
        if($before_migrations){
            while($db = mysqli_fetch_assoc($before_migrations)) {
                foreach($migration_files as $i=>$file){
                    if($db['migrate']==$file){
                        unset($migration_files[$i]);
                        break;
                    }
                }
            }
        }
        return $migration_files;
    }
}


function dirToArray($dir,$invalidDirs=array('.','..')){
    $scan_result = scandir( $dir );
    $result=array();
    foreach ( $scan_result as $key => $value ) {

        if (in_array($value, $invalidDirs) || is_dir($dir . '/' . $value)) {
            continue;
        }
        $result[]=$value;
    }
    return $result;
}


$Migration=new Migration('pmcms');

if(isset($_GET['type']) and $_GET['type']=='down'){

    $Migration->down(isset($_GET['file']) ? $_GET['file'] : '');
}
else{
    $Migration->up();
}

