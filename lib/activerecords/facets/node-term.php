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

use ICanBoogie\Facets\Criterion;
use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\ActiveRecord;

class NodeTermCriterion extends Criterion
{
	static private $tokens = [];

	static protected function generate_token()
	{
		$possible = \ICanboogie\TOKEN_ALPHA;

		for (;;)
		{
			$token = \ICanBoogie\generate_token(6, $possible);

			if (empty(self::$tokens[$token]))
			{
				return self::$tokens[$token] = $token;
			}
		}
	}

	public function alter_query_with_value(Query $query, $value)
	{
		$token = self::generate_token();
		$v_alias = $token . '_v';
		$t_alias = $token . '_t';

		$q = $query->model->models['taxonomy.vocabulary']
		->select("nid, vocabularyslug AS $v_alias, termslug AS $t_alias")
		->join(':taxonomy.terms')
		->join(':taxonomy.terms/nodes');

		$query
		->join($q, [ 'on' => 'nid' ])
		->where([ $v_alias => $this->id, $t_alias => $value]);

		return $query;
	}
}
