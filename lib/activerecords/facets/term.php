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

use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\Facets\Criterion;

/**
 * A criterion for terms.
 */
class TermCriterion extends Criterion
{
	public function alter_query_with_value(Query $query, $value)
	{
		$vocabulary_slug = $this->id;
		$constructor = $query->model->id;

		if (substr($vocabulary_slug, -4, 4) === 'slug')
		{
			$vocabulary_slug = substr($vocabulary_slug, 0, -4);
		}

		$taxonomy_query = $query->model->models['taxonomy.vocabulary']
			->join(':taxonomy.vocabulary/scopes')
			->join(':taxonomy.terms')
			->join(':taxonomy.terms/nodes')
			->select('nid')
			->and([

				'termslug' => $value,
				'vocabularyslug' => $vocabulary_slug,
				'constructor' => $constructor

			]);

		return $query->filter_by_nid($taxonomy_query);
	}
}
