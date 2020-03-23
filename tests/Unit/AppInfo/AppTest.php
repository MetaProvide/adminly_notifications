<?php
/**
 * @author Joas Schilling <coding@schilljs.com>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Notifications\Tests\Unit\AppInfo;

use OC\User\Session;
use OCA\Notifications\App;
use OCA\Notifications\Tests\Unit\TestCase;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserSession;
use OCP\Notification\IManager;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AppTest
 *
 * @group DB
 * @package OCA\Notifications\Tests\AppInfo
 */
class AppTest extends TestCase {
	/** @var IManager|MockObject */
	protected $manager;
	/** @var IRequest|MockObject */
	protected $request;
	/** @var IUserSession|MockObject */
	protected $session;

	protected function setUp(): void {
		parent::setUp();

		$this->manager = $this->createMock(IManager::class);
		$this->request = $this->createMock(IRequest::class);
		$this->session = $this->createMock(Session::class);

		$this->overwriteService(IManager::class, $this->manager);
		$this->overwriteService(IRequest::class, $this->request);
		$this->overwriteService(Session::class, $this->session);
	}

	protected function tearDown(): void {
		$this->restoreService(IManager::class);
		$this->restoreService(IRequest::class);
		$this->restoreService(Session::class);

		parent::tearDown();
	}

	public function testRegisterApp() {
		$this->manager->expects($this->once())
			->method('registerApp')
			->willReturnCallback(function($service) {
				$this->assertSame(App::class, $service);
			});

		include(__DIR__ . '/../../../appinfo/app.php');
	}

	public function dataLoadingJSAndCSS() {
		$user = $this->getMockBuilder(IUser::class)
			->getMock();

		return [
			['/index.php', '/apps/files', $user, true],
			['/index.php', '/apps/files', null, false],
			['/remote.php', '/webdav', $user, false],
			['/index.php', '/s/1234567890123', $user, false],
			['/index.php', '/login/selectchallenge', $user, false],
		];
	}

	/**
	 * @dataProvider dataLoadingJSAndCSS
	 * @param string $scriptName
	 * @param string $pathInfo
	 * @param IUser|null $user
	 * @param bool $scriptsAdded
	 */
	public function testLoadingJSAndCSS($scriptName, $pathInfo, $user, $scriptsAdded) {
		$this->request->expects($this->any())
			->method('getScriptName')
			->willReturn($scriptName);
		$this->request->expects($this->any())
			->method('getPathInfo')
			->willReturn($pathInfo);
		$this->session->expects($this->once())
			->method('getUser')
			->willReturn($user);

		\OC_Util::$scripts = [];
		\OC_Util::$styles = [];

		include(__DIR__ . '/../../../appinfo/app.php');

		if ($scriptsAdded) {
			$this->assertNotEmpty(\OC_Util::$scripts);
			$this->assertNotEmpty(\OC_Util::$styles);
		} else {
			$this->assertEmpty(\OC_Util::$scripts);
			$this->assertEmpty(\OC_Util::$styles);
		}
	}
}
