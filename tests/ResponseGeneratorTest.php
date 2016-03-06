<?php

namespace Tests\Utils;

use CpdnAPI\Utils\ResponseGenerator;

class ResponseGeneratorTest extends \UnitTestCase {
	public function testContentGenerate() {
		$r = ResponseGenerator::generateContent ( "/schemes" );
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json"
				) ,
				"code" => 0,
				"usrMessage" => "Unknown message.",
				"devMessage" => ""
		);
		$this->assertEquals ( $r, $e );
	}
	
	public function testContentGenerateS200() {
		$r = ResponseGenerator::generateContent ( "/schemes", ResponseGenerator::S200 );
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json" 
				) ,
				"code" => 200,
				"usrMessage" => "[OK]",
				"devMessage" => ""
		);
		$this->assertEquals ( $r, $e );
	}
	
	public function testContentGenerateS200DevArgs() {
		$r = ResponseGenerator::generateContent ( "/schemes", ResponseGenerator::S200, "devMsg", array("param" => "value") );
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json"
				) ,
				"code" => 200,
				"usrMessage" => "[OK]",
				"devMessage" => "devMsg",
				"param" => "value"
		);
		$this->assertEquals ( $r, $e );
	}
}