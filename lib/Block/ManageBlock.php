<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Taxonomy\Terms\Block;

use Icybee\Modules\Taxonomy\Terms\Module;

class ManageBlock extends \Icybee\Block\ManageBlock
{
	public function __construct(Module $module, array $attributes = [])
	{
		parent::__construct($module, $attributes + [

			self::T_ORDER_BY => [ 'term', 'asc' ]

		]);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `term`: An instance of {@link TermColumn}.
	 * - `vocabulary_id`: An instance of {@link VidColumn}.
	 * - `popularity`: An instance of {@link PopularityColumn}.
	 *
	 * @inheritdoc
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), [

			'term' => ManageBlock\TermColumn::class,
			'vocabulary_id' => ManageBlock\VidColumn::class,
			'popularity' => ManageBlock\PopularityColumn::class

		]);
	}
}
