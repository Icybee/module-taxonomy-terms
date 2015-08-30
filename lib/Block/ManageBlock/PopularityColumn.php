<?php

namespace Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;

use ICanBoogie\ActiveRecord\Query;
use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Taxonomy\Terms\Term;

/**
 * Representation of the `popularity` column.
 *
 * The column displays the times a term is associated to a node.
 */
class PopularityColumn extends Column
{
	public function __construct(\Icybee\Block\ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, [

			'title' => 'Popularity',
			'class' => 'pull-right',
			'orderable' => true

		]);
	}

	/**
	 * Alerts the query to make the `term_node_count` column.
	 *
	 * @param Query $query
	 *
	 * @return Query
	 */
	public function alter_query(Query $query)
	{
		$term_node_count = $query
			->model->models['taxonomy.terms/nodes']
			->select('vtid, COUNT(nid) AS term_node_count')
			->group('vtid');

		return $query->join($term_node_count, [ 'mode' => 'LEFT', 'on' => 'vtid' ]);
	}

	/**
	 * Orders the records according to their popularity.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_order(Query $query, $order_direction)
	{
		return $query->order("term_node_count " . ($order_direction < 0 ? 'DESC' : 'ASC'));
	}

	/**
	 * @param Term $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return $record->term_node_count;
	}
}
