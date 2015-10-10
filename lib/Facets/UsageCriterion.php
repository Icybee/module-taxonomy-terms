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

use ICanBoogie\Facets\Criterion;
use ICanBoogie\ActiveRecord\Query;

class UsageCriterion extends Criterion
{
	public function alter_query_with_order(Query $query, $order_direction)
	{
		$usage_query = $query
		->model
		->join(':taxonomy.terms/nodes')
		->join(':nodes')
		->select('vtid, COUNT(nid) as `usage`')
		->where('is_online = 1')
		->group('vtid');

		return $query
		->join($usage_query, [ 'as' => 'term_usage' ])
		->order('`usage` ' . ($order_direction < 0 ? 'DESC' : 'ASC'));
	}
}
