<?php

namespace Tests\Utils;

use CpdnAPI\Utils\MetaGenerator;

class MetaGeneratorTest extends \UnitTestCase {
	public function testQuery() {
		$r = MetaGenerator::generate("/schemes");
		$e = array(
				"href" => "https://api.cpdn.sd2.cz/schemes",
				"mediaType" => "application/json"
		);
		$this->assertEquals ( $r, $e );
	}
	
	public function testQueryArgs() {
		$r = MetaGenerator::generate("/schemes", array("param" => "value"));
		$e = array(
				"href" => "https://api.cpdn.sd2.cz/schemes",
				"mediaType" => "application/json",
				"param" => "value"
		);
		$this->assertEquals ( $r, $e );
	}
}