<?php

namespace CpdnAPI\Utils;

use CpdnAPI\Utils\MetaGenerator as MG;

class CollectionGenerator {
	
	const KEY_META = "_meta";
	const KEY_ITEMS = "items";
	const KEY_PAGE_NUMBER = "pageNumber";
	const KEY_PAGE_SIZE = "pageSize";
	const KEY_ITEMS_TOTAL = "itemsTotal";
	const KEY_PAGES_TOTAL = "pagesTotal";
	const KEY_FIRST = "first";
	const KEY_PREVIOUS = "previous";
	const KEY_NEXT = "next";
	const KEY_LAST = "last";
	
	public static function generate($items = array(), $queryUri = "", $itemsTotal = 0, $pagesTotal = 0, $pageNumber = 1, $pageSize = 20) {
		$r = array();
		$r[self::KEY_META] = MG::generate($queryUri);
		$r[self::KEY_ITEMS] = $items;
		$r[self::KEY_PAGE_NUMBER] = $pageNumber;
		$r[self::KEY_PAGE_SIZE] = $pageSize;
		$r[self::KEY_ITEMS_TOTAL] = $itemsTotal;
		$r[self::KEY_PAGES_TOTAL] = $pagesTotal;
		$r[self::KEY_FIRST] = "";
		$r[self::KEY_PREVIOUS] = "";
		$r[self::KEY_NEXT] = "";
		$r[self::KEY_LAST] = "";
		return $r;
	}
}