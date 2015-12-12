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
use ICanBoogie\ActiveRecord;
use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\ActiveRecord\ModelCollection;
use Icybee\Modules\Taxonomy\Terms\Term;
use Icybee\Modules\Taxonomy\Vocabulary\Vocabulary;

class NodeTermCriterion extends Criterion
{
	/**
	 * Alters the query by joining `taxonomy.terms/nodes` and filtering on the resolved term
	 * identifier.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_value(Query $query, $value)
	{
		$alias = 'term_node_' . uniqid();
		$models = $query->model->models;
		$term_id = is_numeric($value)
			? $value
			: $this->resolve_term_id($this->id, $value, $models);

		$query
		->join(':taxonomy.terms/nodes', [ 'on' => 'nid', 'as' => $alias ])
		->where("$alias.term_id = ?", $term_id);

		return $query;
	}

	/**
	 * Resolves term identifier from slug.
	 *
	 * @param string $vocabulary_slug
	 * @param string $term_slug
	 * @param ModelCollection $models
	 *
	 * @return int Term identifier.
	 */
	private function resolve_term_id($vocabulary_slug, $term_slug, ModelCollection $models)
	{
		return $models['taxonomy.vocabulary']
		->join(':taxonomy.terms')
		->select("term_id")
		->where([ Vocabulary::VOCABULARY_SLUG => $vocabulary_slug, Term::TERM_SLUG => $term_slug ])
		->rc;
	}
}
