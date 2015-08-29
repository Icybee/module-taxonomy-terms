<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Taxonomy\Terms;

use Icybee;

$hooks = Hooks::class . '::';

return [

	Icybee\Modules\Nodes\Operation\DeleteOperation::class . '::process' => $hooks . 'on_nodes_delete'

];
