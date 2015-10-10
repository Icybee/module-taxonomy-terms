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

use ICanBoogie\Updater\Update;

/**
 * - Renames column `vtid` as `term_id`.
 *
 * @module taxonomy.terms
 */
class Update20151011 extends Update
{
	public function update_column_term_id()
	{
		$this->module->models['nodes']
			->assert_has_column('vtid')
			->rename_column('vtid', 'term_id');

		$this->module->model
			->assert_has_column('vtid')
			->rename_column('vtid', 'term_id');
	}
}
