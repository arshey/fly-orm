<?php

/*
 * FLY ORM is a simple orm php
 * to make pdo request easly.
 *
 * (c) José Amani <amani.jose@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Src\orm;


class FlyBuilder{

	protected $table;
	protected $child_table;
	protected $sql;
	protected $orderBy = "";
	protected $where = "";
	protected $data = [];
	protected $fields;
	protected $conditions = [];
	protected $limit;
	protected $db;
	protected $config;

	public function __construct(){

		
	}



	/**
    * Get the database settings
	* and make the pdo connexion
	* @param array connexion settings
	* @return object | boolean
    **/
	public function setup($conf = []){
		if($conf == []){
			return false;
		}else{
			
			$config  = $conf;
		}
		$this->db = new \PDO($config['TYPE'].':host='.$config['HOSTNAME'].';dbname='.$config['DBNAME'],
  		      							$config['USERNAME'],$config['PASSWORD']);

		return $this;
	} 
	

	/**
    * initialize the database table
	* @param string $table 
    * @return this 
    **/
	public function table($table){
		$this->table = $table;
		return $this;
	}

	/**
    * initialize fields table
	* @param string $fields 
    * @return this 
    **/
	public function select($fields = ""){
		if ($fields == "") {
			$this->fields = "*";
		}else{
			$this->fields = $fields;
			$tab = $fields;
			$fields = '';

			foreach ($tab as $value) {
				
				$fields .= "$value,";
			}

			$fields = substr($fields,0,-1);
		}


		return $this;
	}
	/**
    * execute and get results of the query
	* @param int $begin limit 
	* @param int $end offset
    * @return object $result 
    **/
	public function get($begin = "" , $end = ""){
		if($this->sql == ''){
			$this->sql = "SELECT * FROM ".$this->table;
		}
		if($this->where != ""){
			$this->sql .= $this->where;
		}
		if ($this->orderBy != "") {
			$this->sql .= " ORDER BY $this->orderBy";
		}

		if(empty($end) and !empty($begin)){
			$begin = (int) $begin; 
			$this->limit = " LIMIT $begin";
		}elseif(!empty($end) and !empty($begin)){
			$begin = (int) $begin;
			$end   = (int) $end; 
			$this->limit = " LIMIT $begin , $end";
		}

		if($this->limit != ""){
			$this->sql .= $this->limit;
		}
		
		$pdo = $this->db->prepare($this->sql);
		if (count($this->conditions) > 0) {
			$pdo->execute($this->conditions);
		}else{
			$pdo->execute();
		}
		
		return $pdo->fetchAll(\PDO::FETCH_OBJ);
	}

	/**
    * execute and get one result of the query
    * @return object $result
    **/

	public function first(){
		if($this->sql == ''){
			$this->sql = "SELECT * FROM ".$this->table;
		}
		if($this->where != ""){
			$this->sql .= $this->where;
		}
		if ($this->orderBy != "") {
			$this->sql .= " ORDER BY $this->orderBy";
		}

		$pdo = $this->db->prepare($this->sql);
		if (count($this->conditions) > 0) {
			$pdo->execute($this->conditions);
		}else{
			$pdo->execute();
		}
		
		return $pdo->fetch(\PDO::FETCH_OBJ);
	}

	/**
    * create a new entries in the table
	* @param array $values
    * @return boolean 
    **/
	public function create($values = []){
		if(empty($values)){
			$values = $this->data;
			
		}

		$this->sql = "INSERT INTO ".$this->table." (";
		foreach ($values as $k => $v) {
			$this->sql .= $k .',';
			array_push($this->conditions, $v);
		}
		
		$this->sql = substr($this->sql,0,-1);
		$this->sql .=") VALUES (";

		foreach ($values as $k => $v) {
			$this->sql .= '?,';
		}
		$this->sql = substr($this->sql,0,-1);

		$this->sql .= ")";
		
	 	$pdo = $this->db->prepare($this->sql);
		return $pdo->execute($this->conditions);
	}

	/**
    * initialize the table and the fields 
	* which be updated
    * @return $this
    **/
	public function refresh($table,$id){
		$this->table = $table;
		$this->id = $id;
		return $this;
	}

	/**
    * execute the update of the table
    * @return boolean
    **/

	public function update($value = []){
		if(empty($values)){
			$values = $this->data;
			
		}
		
		$this->sql = "UPDATE ".$this->table." SET ";
		foreach ($values as $k => $v) {
			$this->sql .= $k .' = ?,';
			array_push($this->conditions, $v);
		}
		
		$this->sql = substr($this->sql,0,-1);
		$t = explode("WHERE", $this->sql);
		if (count($t) > 1) {
			$this->sql .=" AND id = ?";
			array_push($this->conditions, $this->id);
		}else{
			$this->sql .=" WHERE id = ?";
			array_push($this->conditions, $this->id);
		}
		
		
		$pdo = $this->db->prepare($this->sql);
		return $pdo->execute($this->conditions);
	}

	/**
    * make query condition 
	* @param array $conditions
    * @return $this
    **/

	public function where($conditions){
		foreach($conditions as $k => $v){
		
			$t = explode("WHERE", $this->where);
			if (count($t) > 1) {
				if(strstr('=', $k)){
					$this->where .= " AND $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strstr('>',$k)){
					
					$this->where .= " AND $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strstr($k,'<')){
					$this->where .= " AND $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strstr($k,'<=')){
					$this->where .= " AND $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strstr($k,'>=')){
					$this->where .= " AND $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strstr($k,'LIKE')){
					$this->where .= " AND $k ?";
					array_push($this->conditions, $v);
				}else{
					$this->where .= " AND $k = ?";
					array_push($this->conditions, $v);
				}
				
			}else{
				if(strpos($k, '=')){
					$this->where .= " WHERE $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strpos($k,'>')){
					$this->where .= " WHERE $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strpos($k,'<')){
					$this->where .= " WHERE $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strpos($k,'<=')){
					$this->where .= " WHERE $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strpos($k,'>=')){
					$this->where .= " WHERE $k ?";
					array_push($this->conditions, $v);
				}
				elseif(strpos($k,'LIKE')){
					$this->where .= " WHERE $k ?";
					array_push($this->conditions, $v);
				}
				else{
					$this->where .= " WHERE $k = ?";
					array_push($this->conditions, $v);
				}
			}
		}
		
		return $this;
	}

	/**
    * make query order
	* @param string $order
    * @return $this
    **/
	public function orderBy($order = ""){
		$this->orderBy = $order;
		return $this;
	}

	/**
    * limit the query results
	* @param int $begin
	* @param int $end offset
    * @return $this
    **/
	public function limit($begin = "",$end = ""){
		if(empty($end) and !empty($begin)){
			$begin = (int) $begin; 
			$this->limit = " LIMIT $begin";
			return $this;
		}elseif(!empty($end) and !empty($begin)){
			$begin = (int) $begin;
			$end   = (int) $end; 
			$this->limit = " LIMIT $begin , $end";
			return $this;
		}else{
			return false;
		}
	}

	/**
    * count query results 
	* @param string table name
	* @param string field name
    * @return object| boolean result
    **/
	public function count($table,$fields = "*"){
		$this->table = $table;
		$this->sql = "SELECT count($fields) as number FROM $this->table";
		
		if($this->where != ""){
			$this->sql .= $this->where;
		}
		
		$pdo = $this->db->prepare($this->sql);
		if (count($this->conditions) > 0) {
			$pdo->execute($this->conditions);
		}else{
			$pdo->execute();
		}
		$res = $pdo->fetch(\PDO::FETCH_OBJ);
		return $res;

	}

	/**
    * delete entries table
	* @param string table name 
	* @param string id 
    * @return object | boolean 
    **/
	public function trash($table,$id = ""){
		$this->table = $table;
		$this->id = $id;
		
		if ($this->id != "") {
	
			$this->sql = "DELETE FROM ".$this->table." WHERE id = ?";
			
			$pdo = $this->db->prepare($this->sql);
			$res = $pdo->execute(array($this->id));
			return $res;
		}else{

			return false;
		}
		
	}

	/**
    * inner join query  
	* @param string join table name
	* @param string join condition
    * @return $this
    **/
	public function innerjoin($table,$conditions){
		$t = explode("INNER JOIN", $this->sql);
		if (count($t) > 1) {
			$this->sql .= " INNER JOIN $table ON $conditions";
			return $this;
		}else{
			$this->sql = "SELECT * FROM $this->table INNER JOIN $table ON $conditions";
			return $this;
		}
	}

	/**
    * left join query  
	* @param string join table name
	* @param string join condition
    * @return $this
    **/
	public function leftjoin($table,$conditions){
		$t = explode("LEFT JOIN", $this->sql);
		if (count($t) > 1) {
			$this->sql .= " LEFT JOIN $table ON $conditions";
			return $this;
		}else{
			$this->sql = "SELECT * FROM $this->table LEFT JOIN $table ON $conditions";
			return $this;
		}
	}

	/**
    * right join query  
	* @param string join table name
	* @param string join condition
    * @return $this
    **/
	public function rightjoin($table,$conditions){
		$t = explode("INNER JOIN", $this->sql);
		if (count($t) > 1) {
			$this->sql .= " INNER JOIN $table ON $conditions";
			return $this;
		}else{
			$this->sql = "SELECT * FROM $this->table INNER JOIN $table ON $conditions";
			return $this;
		}
	}

	
	public function __set($name, $value) {
		
        $this->data[$name] = $value;
    }
	
  
}

/**
* Class to make FACADE
**/
namespace Src\orm;

class Fly{

    public static function __callStatic($name, $arguments){
		$query = new \Src\orm\FlyBuilder(); 
        return call_user_func_array([$query, $name], $arguments);
    }

}

