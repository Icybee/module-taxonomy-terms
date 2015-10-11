<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Taxonomy\Terms\Facets;

use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\Facets\Criterion;

/**
 * A criterion for terms.
 */
class TermCriterion extends Criterion
{
	public function alter_query_with_value(Query $query, $value)
	{
		return $query->and([

			is_numeric($value) ? 'term_id' : 'term_slug' => $value

		]);
	}
}
