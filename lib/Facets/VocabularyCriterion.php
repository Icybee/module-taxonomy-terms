<?php

namespace Icybee\Modules\Taxonomy\Terms\Facets;

use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\Facets\Criterion;

class VocabularyCriterion extends Criterion
{
	/**
	 * Orders the records according to vocabulary name.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_order(Query $query, $order_direction)
	{
		$vocabulary_names_alias = 'vocabulary' . uniqid();
		$vocabulary_names = $query->model->models['taxonomy.vocabulary']
			->select('vocabulary_id, vocabulary');

		return $query
			->join($vocabulary_names, [ 'on' => 'vocabulary_id', 'as' => $vocabulary_names_alias ])
			->order("$vocabulary_names_alias.vocabulary " . ($order_direction < 0 ? 'DESC' : 'ASC') );
	}
}
