<?php

namespace CpdnAPI\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use \Exception;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class NotFoundPlugin extends Plugin {
	
	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event        	
	 * @param Dispatcher $dispatcher        	
	 */
	public function beforeException(Event $event, MvcDispatcher $dispatcher, Exception $exception) {
		if ($exception instanceof DispatcherException) {
			switch ($exception->getCode ()) {
				case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND :
				case Dispatcher::EXCEPTION_ACTION_NOT_FOUND :
					$dispatcher->forward ( array (
							'controller' => 'errors',
							'action' => 'e404' 
					) );
					return false;
			}
		}
		/*
		 * echo "<pre>";
		 * var_dump($exception);
		 * echo "</pre>";
		 * exit();
		 */
		$dispatcher->forward ( array (
				'controller' => 'errors',
				'action' => 'e500' 
		) );
		return false;
	}
}
