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
use ICanBoogie\ActiveRecord;

class NodeTermCriterion extends Criterion
{
	public function alter_query_with_value(Query $query, $value)
	{
		$v_alias = 'vocabulary' . uniqid();
		$t_alias = 'term' . uniqid();

		$q = $query->model->models['taxonomy.vocabulary']
		->select("nid, vocabulary_slug AS $v_alias, term_id AS $t_alias")
		->join(':taxonomy.terms')
		->join(':taxonomy.terms/nodes');

		$query
		->join($q, [ 'on' => 'nid' ])
		->where([ $v_alias => $this->id, $t_alias => $value]);

		return $query;
	}
}
