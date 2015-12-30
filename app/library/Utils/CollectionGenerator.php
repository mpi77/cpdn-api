<?php

namespace CpdnAPI\Utils;

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
	
	public static function generate($items = array()) {
		$r = array();
		$r[self::KEY_META] = "";
		$r[self::KEY_ITEMS] = $items;
		$r[self::KEY_PAGE_NUMBER] = "";
		$r[self::KEY_PAGE_SIZE] = "";
		$r[self::KEY_ITEMS_TOTAL] = "";
		$r[self::KEY_PAGES_TOTAL] = "";
		$r[self::KEY_FIRST] = "";
		$r[self::KEY_PREVIOUS] = "";
		$r[self::KEY_NEXT] = "";
		$r[self::KEY_LAST] = "";
		return $r;
	}
}