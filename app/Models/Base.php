<?php namespace YiZan\Models;

use Illuminate\Database\Eloquent\Model;
use YiZan\Relations\HasManyCount;

class Base extends Model 
{
	/**
     * 禁用
	 */
	const STATUS_DISABLED	= 0;
	/**
     * 启用
	 */
	const STATUS_ENABLED	= 1;
	/**
     * 未通过
	 */
	const STATUS_NOT_BY		= -1;
	/**
     * 待审核
	 */
	const STATUS_AUDITING	= -2;
	
	public static $snakeAttributes = false;
	private $_relationCounts = [];
	private $_hidden = [];
	
	public $timestamps = false;
	
	public function getTable(){
		return $this->table ? $this->table : strtolower(snake_case(class_basename($this)));
	}

	/**
	 * 重写模型类,在输入数组时将属性名转为驼峰式命名
	 * @param  array  $values [description]
	 * @return [type]         [description]
	 */
	protected function getArrayableItems(array $values){
		if (count($this->visible) > 0) {
			$values = $this->camelCaseAttributes(array_intersect_key($values, array_flip($this->visible)));
		} else {
			$values = $this->camelCaseAttributes($values);
		}
		
		$values = array_diff_key($values, $this->_hidden);
		if (count($this->hidden) == 0) {
			return $values;
		}
		return array_diff_key($values, array_flip($this->hidden));
	}

	private function camelCaseAttributes(array $values) {
		foreach ($values as $key => $value) {
			if (strpos($key, '_') && !in_array($key, $this->hidden)) {
				$values[camel_case($key)] = $values[$key];
				$this->_hidden[$key] = 0;
			}
		}
		return $values;
	}

	public function hasManyCount($related, $foreignKey = null, $localKey = null) {
		$foreignKey = $foreignKey ?: $this->getForeignKey();

		$instance = new $related;

		$localKey = $localKey ?: $this->getKeyName();

		return new HasManyCount($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
	}

	public function setCountRelation($relation, $value) {
		$this->_relationCounts[$relation] = $value;
	}

	public function toArray() {
		$attributes = parent::toArray();
		return array_merge($attributes, $this->countRelationsToArray());
	}

	private function countRelationsToArray() {
		$attributes = array();
		foreach ($this->_relationCounts as $key => $value) {
			$attributes[$key] = (int)$value;
		}
		return $attributes;
	}
}
