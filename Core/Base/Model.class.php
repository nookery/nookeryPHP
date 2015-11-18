<?php

/**
 * 模型
 */

namespace Core\Base;

class Model {
	public function __construct() {
		$appConfig = $GLOBALS['appConfig'];
		$dbms = $appConfig['database']['type']; //数据库类型
		$host = $appConfig['database']['host']; //数据库主机名
		$dbName = $appConfig['database']['database']; //使用的数据库
		$user = $appConfig['database']['user']; //数据库连接用户名
		$pass = $appConfig['database']['password']; //对应的密码
		$dsn = "$dbms:host=$host;dbname=$dbName";

		try {
			$this->db = new \PDO($dsn, $user, $pass); //初始化一个PDO对象
		} catch (PDOException $e) {
			die ("Error!: " . $e->getMessage() . "<br/>");
		}
		$this->db->exec("SET names utf8");
	}

	public function __destruct() {
		$this->db = null;
	}

	/**
	 * 返回一个表中的所有记录
	 * @return array
	 */
	public function all() {
		$result = $this->db->query("
			SELECT *
			FROM {$this->table}
		");

		// 设置获取结果集的返回值的类型，同样类型还有：
		// PDO::FETCH_ASSOC -- 关联数组形式
		// PDO::FETCH_NUM -- 数字索引数组形式
		// PDO::FETCH_BOTH -- 两者数组形式都有，这是缺省的
		// PDO::FETCH_OBJ -- 按照对象的形式
		$result->setFetchMode(\PDO::FETCH_ASSOC);

		$result = $result->fetchAll();
		return $result;
	}

	public function exec($query) {
		$count = $this->db->exec($query);
		return $count;
	}

	public function fields() {
		$result = $this->db->query('DESC sites');
		$result->setFetchMode(\PDO::FETCH_ASSOC);
		$result = $result->fetchAll();

		$fields = array();
		foreach ($result as $r) {
			$fields[] = $r['Field'];
		}

		return $fields;
	}

	/**
	 * 新增数据
	 * @access public
	 * @param arrar $data 数据
	 * @return mixed
	 */
	public function insert($data = array()) {
		$fields = $this->fields(); //当前表的所有字段
		$fieldsInsert = array(); //将要赋值的字段
		foreach ($data as $k=>&$d) {
			if (in_array($k, $fields)) {
				$fieldsInsert[] = $k;
				$d = "'{$d}'";
			} else {
				unset($data[$key]);
			}
		}

		$fieldsInsert = implode(',', $fieldsInsert);
		$values = implode(',', $data);
		$this->query("
			INSERT INTO {$this->table}
			({$fieldsInsert})
			VALUES
			({$values})
		");
		return $this->db->lastInsertId();
	}

	/**
	 * 返回一条记录，如
	 * array(13) {
	 *  ["id"]=>
	 *  string(2) "35"
	 *  ["nickname"]=>
	 *  string(3) "安"
	 * }
	 * @param  string $query sql语句
	 * @return array
	 */
	public function find($query) {
		$rs = $this->db->query($query);

		// 设置获取结果集的返回值的类型，同样类型还有：
		// PDO::FETCH_ASSOC -- 关联数组形式
		// PDO::FETCH_NUM -- 数字索引数组形式
		// PDO::FETCH_BOTH -- 两者数组形式都有，这是缺省的
		// PDO::FETCH_OBJ -- 按照对象的形式
		$rs->setFetchMode(\PDO::FETCH_ASSOC);
		$rs = $rs->fetch();
		return $rs;
	}

	/**
	 * 返回一个col的值
	 * @param  string $query sql语句
	 * @return array
	 */
	public function oneCol($query) {
		$result = $this->db->query($query);

		// 设置获取结果集的返回值的类型，同样类型还有：
		// PDO::FETCH_ASSOC -- 关联数组形式
		// PDO::FETCH_NUM -- 数字索引数组形式
		// PDO::FETCH_BOTH -- 两者数组形式都有，这是缺省的
		// PDO::FETCH_OBJ -- 按照对象的形式
		$result->setFetchMode(\PDO::FETCH_ASSOC);
		$result = $result->fetchColumn(0);
		return $result;
	}

	/**
	 * 执行查询语句
	 * @param  string $query sql语句
	 * @return array
	 */
	public function query($query) {
		$result = $this->db->query($query);

		// 设置获取结果集的返回值的类型，同样类型还有：
		// PDO::FETCH_ASSOC -- 关联数组形式
		// PDO::FETCH_NUM -- 数字索引数组形式
		// PDO::FETCH_BOTH -- 两者数组形式都有，这是缺省的
		// PDO::FETCH_OBJ -- 按照对象的形式
		$result->setFetchMode(\PDO::FETCH_ASSOC);
		$result = $result->fetchAll();
		return $result;
	}

	/**
	 * 执行查询语句，返回数组
	 * @param  string $query sql语句
	 * @return array
	 */
	public function select($query) {
		$result = $this->db->query($query);

		// 设置获取结果集的返回值的类型，同样类型还有：
		// PDO::FETCH_ASSOC -- 关联数组形式
		// PDO::FETCH_NUM -- 数字索引数组形式
		// PDO::FETCH_BOTH -- 两者数组形式都有，这是缺省的
		// PDO::FETCH_OBJ -- 按照对象的形式
		$result->setFetchMode(\PDO::FETCH_ASSOC);
		$result = $result->fetchAll();
		return $result;
	}

	public function update($query) {
		$count = $this->db->exec($query);
		return $count;
	}
}
