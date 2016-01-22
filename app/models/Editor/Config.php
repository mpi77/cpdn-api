<?php

namespace CpdnAPI\Models\Editor;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Config extends Model {
	
	/**
	 *
	 * @var string
	 *
	 */
	public $key;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $value;
	
	public function validation() {
		$this->validate ( new Uniqueness ( array (
				"field" => "key",
				"message" => "Value of field 'key' is already present in another record" 
		) ) );
		if ($this->validationHasFailed () == true) {
			return false;
		}
	}
	public function initialize() {
		$this->setConnectionService ( 'editorDb' );
	}
	public function columnMap() {
		return array (
				'key' => 'key',
				'value' => 'value' 
		);
	}
	
	/**
	 * Get config in defined output structure.
	 *
	 * @param Config $config        	
	 * @return array
	 */
	public static function getConfig(Config $config) {
		return array (
				"key" => $config->key,
				"value" => $config->value 
		);
	}
}
