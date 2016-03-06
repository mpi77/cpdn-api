<?php

namespace Tests\Utils;

use CpdnAPI\Utils\ItemGenerator;

class ItemGeneratorTest extends \UnitTestCase {
	public function testItemGenerate() {
		$r = ItemGenerator::generate("/schemes");
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json"
				) 
		);
		$this->assertEquals ( $r, $e );
	}
	
	public function testItemGenerateMetaArgs() {
		$r = ItemGenerator::generate("/schemes", array("param" => "value"));
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json",
						"param" => "value"
				)
		);
		$this->assertEquals ( $r, $e );
	}
	
	public function testItemGenerateBodyArgs() {
		$r = ItemGenerator::generate("/schemes", array(), array("param" => "value"));
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json"
				),
				"param" => "value"
		);
		$this->assertEquals ( $r, $e );
	}
}