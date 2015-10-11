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
 * - Rename column `vtid` as `term_id`.
 * - Rename column `vid` as `vocabulary_id`.
 * - Rename column `termslug` as `term_slug`.
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

	public function update_column_term_slug()
	{
		$this->module->model
			->assert_has_column('termslug')
			->rename_column('termslug', 'term_slug');
	}

	public function update_column_vocabulary_id()
	{
		$this->module->model
			->assert_has_column('vid')
			->rename_column('vid', 'vocabulary_id');
	}
}
