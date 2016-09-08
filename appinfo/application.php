<?php

/**
 * ownCloud - bookmarks
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 * @author Marvin Thomas Rabe <mrabe@marvinrabe.de>
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @author Stefan Klemm <mail@stefan-klemm.de>
 * @copyright (c) 2011, Marvin Thomas Rabe
 * @copyright (c) 2011, Arthur Schiwon
 * @copyright (c) 2014, Stefan Klemm
 */

namespace OCA\Bookmarks\AppInfo;

use \OCP\AppFramework\App;
use \OCP\IContainer;
use \OCA\Bookmarks\Controller\WebViewController;
use OCA\Bookmarks\Controller\Rest\TagsController;
use OCA\Bookmarks\Controller\Rest\BookmarkController;
use OCA\Bookmarks\Controller\Rest\PublicController;
use OCP\IUser;

class Application extends App {

	public function __construct(array $urlParams = array()) {
		parent::__construct('bookmarks', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 * @param IContainer $c The Container instance that handles the request
		 */
		$container->registerService('WebViewController', function($c) {
			/** @var IUser|null $user */
			$user = $c->query('ServerContainer')->getUserSession()->getUser();
			$uid = is_null($user) ? null : $user->getUID();

			/** @var IContainer $c */
			return new WebViewController(
				$c->query('AppName'),
				$c->query('Request'),
				$user,
				$c->query('ServerContainer')->getURLGenerator(),
				$c->query('ServerContainer')->getDb()
			);
		});

		$container->registerService('BookmarkController', function($c) {
			if(method_exists($c->query('ServerContainer'), 'getL10NFactory')) {
				$l = $c->query('ServerContainer')->getL10NFactory()->get('bookmarks');
			} else {
				// OC 8.1 compatibility
				$l = new \OC_L10N('bookmarks');
			}

			/** @var IContainer $c */
			return new BookmarkController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ServerContainer')->getUserSession()->getUser()->getUID(),
				$c->query('ServerContainer')->getDb(),
				$l
			);
		});

		$container->registerService('TagsController', function($c) {
			/** @var IContainer $c */
			return new TagsController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ServerContainer')->getUserSession()->getUser()->getUID(),
				$c->query('ServerContainer')->getDb()
			);
		});

		$container->registerService('PublicController', function($c) {
			/** @var IContainer $c */
			return new PublicController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ServerContainer')->getDb(),
				$c->query('ServerContainer')->getUserManager()
			);
		});

	}

}
