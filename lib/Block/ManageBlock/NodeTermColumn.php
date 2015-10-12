<?php

namespace Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;

use ICanBoogie\ActiveRecord\Query;

use Icybee\Block\ManageBlock;
use Icybee\Modules\Taxonomy\Vocabulary\Vocabulary;

class NodeTermColumn extends ManageBlock\Column
{
	/**
	 * @var Vocabulary
	 */
	private $vocabulary;

	/**
	 * Query prefix.
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * @inheritdoc
	 */
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		/* @var $vocabulary Vocabulary */

		$this->vocabulary = $vocabulary = $this->app
			->models['taxonomy.vocabulary']
			->filter_by_vocabulary_slug($id)
			->one;

		$this->prefix = 'term_' . \ICanBoogie\generate_token(8, \ICanBoogie\TOKEN_ALPHA . \ICanBoogie\TOKEN_NUMERIC) . '_';

		parent::__construct($manager, $id, $options + [

			'title' => $vocabulary->vocabulary,
			'orderable' => true

		]);
	}

	/**
	 * @inheritdoc
	 */
	public function alter_conditions(array &$conditions, array $modifiers)
	{
		$id = $this->id;

		if (isset($modifiers[$id]))
		{
			$value = $modifiers[$id];

			if ($value)
			{
				$conditions[$id] = $modifiers[$id];
			}
			else
			{
				unset($conditions[$id]);
			}
		}

		return $conditions;
	}

	public function alter_query(Query $query)
	{
		$prefix = $this->prefix;

		$term_query = $this->app
			->models['taxonomy.terms/nodes']
			->select("nid, term_id AS {$prefix}term_id, term AS {$prefix}term")
			->filter_by_vocabulary_id($this->vocabulary->vocabulary_id);

		return $query->join($term_query, [ 'on' => 'nid', 'mode' => 'LEFT', 'as' => $prefix ]);
	}

	public function alter_query_with_value(Query $query, $filter_value)
	{
		return $query->and([ "{$this->prefix}term_id" => $filter_value ]);
	}

	/**
	 * @inheritdoc
	 */
	public function alter_query_with_order(Query $query, $order_direction)
	{
		return $query->order("{$this->prefix}term " . ($order_direction < 0 ? 'DESC' : 'ASC'));
	}

	/**
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return new ManageBlock\FilterDecorator($record, $this->id, $this->is_filtering,
			$record->{ $this->prefix . 'term' } ?: '', $record->{ $this->prefix . 'term_id' } ?: '');
	}
}
