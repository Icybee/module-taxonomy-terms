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
use Icybee\Block\ManageBlock\EditDecorator;
use Icybee\Modules\Taxonomy\Terms\Term;

class TermColumn extends Column
{
	public function __construct(\Icybee\Block\ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

				'title' => 'Term'

			]);
	}

	/**
	 * @param Term $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return new EditDecorator($record->term, $record);
	}
}
