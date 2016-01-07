<?php

namespace CpdnAPI\Utils;

use CpdnAPI\Utils\Paginator;
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
	public static function generate($items = array(), $queryUri = "", $itemsTotal = 0, $pagesTotal = 0, $pageNumber = 1, $pageSize = 20, $pageLinkNumbers = array("first" => 0,"previous" => 0,"next" => 0,"last" => 0)) {
		$r = array ();
		$r [self::KEY_META] = MG::generate ( $queryUri );
		$r [self::KEY_ITEMS] = $items;
		$r [self::KEY_PAGE_NUMBER] = $pageNumber;
		$r [self::KEY_PAGE_SIZE] = $pageSize;
		$r [self::KEY_ITEMS_TOTAL] = $itemsTotal;
		$r [self::KEY_PAGES_TOTAL] = $pagesTotal;
		$r [self::KEY_FIRST] = self::getPageMetaLink ( $queryUri, $pageLinkNumbers ["first"], $pageSize );
		$r [self::KEY_PREVIOUS] = self::getPageMetaLink ( $queryUri, $pageLinkNumbers ["previous"], $pageSize );
		$r [self::KEY_NEXT] = self::getPageMetaLink ( $queryUri, $pageLinkNumbers ["next"], $pageSize );
		$r [self::KEY_LAST] = self::getPageMetaLink ( $queryUri, $pageLinkNumbers ["last"], $pageSize );
		return $r;
	}
	public static function getPageMetaLink($queryUri, $pageNumber, $pageSize) {
		if (mb_strpos ( $queryUri, Paginator::URL_QUERY_PARAM_PAGE_NUMBER ) > 0) {
			$queryUri = preg_replace ( sprintf ( "/%s=[1-9]\d*/", Paginator::URL_QUERY_PARAM_PAGE_NUMBER ), sprintf ( "%s=%d", Paginator::URL_QUERY_PARAM_PAGE_NUMBER, $pageNumber ), $queryUri );
		} else {
			if (mb_strpos ( $queryUri, "?" ) > 0) {
				$queryUri = sprintf ( "%s&%s=%d", $queryUri, Paginator::URL_QUERY_PARAM_PAGE_NUMBER, $pageNumber );
			} else {
				$queryUri = sprintf ( "%s?%s=%d", $queryUri, Paginator::URL_QUERY_PARAM_PAGE_NUMBER, $pageNumber );
			}
		}
		
		if (mb_strpos ( $queryUri, Paginator::URL_QUERY_PARAM_PAGE_SIZE ) > 0) {
			$queryUri = preg_replace ( sprintf ( "/%s=[1-9]\d*/", Paginator::URL_QUERY_PARAM_PAGE_SIZE ), sprintf ( "%s=%d", Paginator::URL_QUERY_PARAM_PAGE_SIZE, $pageSize ), $queryUri );
		} else {
			if (mb_strpos ( $queryUri, "?" ) > 0) {
				$queryUri = sprintf ( "%s&%s=%d", $queryUri, Paginator::URL_QUERY_PARAM_PAGE_SIZE, $pageSize );
			} else {
				$queryUri = sprintf ( "%s?%s=%d", $queryUri, Paginator::URL_QUERY_PARAM_PAGE_SIZE, $pageSize );
			}
		}
		
		$r = array (
				MG::KEY_META => MG::generate ( $queryUri ) 
		);
		return $r;
	}
}