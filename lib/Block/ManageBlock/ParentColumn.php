<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;

use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;

class ParentColumn extends Column
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, [

			'title' => 'Popularity',
			'class' => 'pull-right',
			'orderable' => true

		]);
	}
}
