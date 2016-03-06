<?php

namespace Tests\Utils;

use CpdnAPI\Utils\CollectionGenerator;

class CollectionGeneratorTest extends \UnitTestCase {
	public function testEmptyCollection() {
		$r = CollectionGenerator::generate ( array (), "/schemes" );
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json" 
				),
				"items" => array (),
				"pageNumber" => 1,
				"pageSize" => 20,
				"itemsTotal" => 0,
				"pagesTotal" => 0,
				"first" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				),
				"next" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				),
				"previous" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				),
				"last" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				) 
		);
		$this->assertEquals ( $r, $e );
	}
	public function testFullCollection() {
		$r = CollectionGenerator::generate ( array (
				"field1",
				"field2",
				"field3" 
		), "/schemes" );
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes",
						"mediaType" => "application/json" 
				),
				"items" => array (
						"field1",
						"field2",
						"field3" 
				),
				"pageNumber" => 1,
				"pageSize" => 20,
				"itemsTotal" => 0,
				"pagesTotal" => 0,
				"first" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				),
				"next" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				),
				"previous" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				),
				"last" => array (
						"_meta" => array (
								"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=0&pageSize=20",
								"mediaType" => "application/json" 
						) 
				) 
		);
		$this->assertEquals ( $r, $e );
	}
	public function testPageMetalink() {
		$r = CollectionGenerator::getPageMetaLink ( "/schemes", 1, 20 );
		$e = array (
				"_meta" => array (
						"href" => "https://api.cpdn.sd2.cz/schemes?pageNumber=1&pageSize=20",
						"mediaType" => "application/json" 
				) 
		);
		$this->assertEquals ( $r, $e );
	}
}