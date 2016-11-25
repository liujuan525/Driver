<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Joseph Maxwell
 * @copyright SwiftOtter Studios, 11/25/16
 * @package default
 **/

namespace Driver\Tests\Unit\Enines\MySql\Sandbox;

use Driver\Engines\MySql\Sandbox\Sandbox;
use Driver\Tests\Unit\Helper\DI;

class SandboxTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstanceActiveReturnsTrue()
    {
        $sandbox = DI::getContainer()->make(Sandbox::class, ['disableInstantiation' => true]);
        $this->assertTrue($sandbox->getInstanceActive());
    }
}