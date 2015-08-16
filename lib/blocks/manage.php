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

use Icybee\Modules\Taxonomy\Terms\ManageBlock\PopularityColumn;
use Icybee\Modules\Taxonomy\Terms\ManageBlock\TermColumn;
use Icybee\Modules\Taxonomy\Terms\ManageBlock\VidColumn;

class ManageBlock extends \Icybee\ManageBlock
{
	public function __construct(Module $module, array $attributes = [])
	{
		parent::__construct($module, $attributes + [

			self::T_ORDER_BY => [ 'term', 'asc' ]

		]);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `term`: An instance of {@link TermColumn}.
	 * - `vid`: An instance of {@link VidColumn}.
	 * - `popularity`: An instance of {@link PopularityColumn}.
	 *
	 * @inheritdoc
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), [

			'term' => TermColumn::class,
			'vid' => VidColumn::class,
			'popularity' => PopularityColumn::class

		]);
	}
}

namespace Icybee\Modules\Taxonomy\Terms\ManageBlock;

use ICanBoogie\ActiveRecord\Query;

use Icybee\ManageBlock\Column;
use Icybee\ManageBlock\FilterDecorator;
use Icybee\ManageBlock\EditDecorator;
use Icybee\Modules\Taxonomy\Terms\Term;

class TermColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options = [])
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

/**
 * Representation of the `vid` column.
 */
class VidColumn extends Column
{
	public function __construct(\Icybee\Modules\Taxonomy\Terms\ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

			'title' => 'Vocabulary',
			'orderable' => true

		]);
	}

	/**
	 * Extends the "vid" column by providing vocabulary filters.
	 */
	protected function get_options()
	{
		// Move this to render_header() when it's actually used

		$keys = $this->manager->module->model->select('DISTINCT vid')->all(\PDO::FETCH_COLUMN);

		if (count($keys) < 2)
		{
			$this->orderable = false;

			return null;
		}

		// /

		return $this->app->models['taxonomy.vocabulary']
		->select('CONCAT("?vid=", vid), vocabulary')
		->where([ 'vid' => $keys ])
		->order('vocabulary')
		->pairs;
	}

	/**
	 * Alters the query with the 'vid' filter.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_filter(Query $query, $filter_value)
	{
		if ($filter_value)
		{
			$query->filter_by_vid($filter_value);
		}

		return $query;
	}

	/**
	 * Orders the records according to vocabulary name.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_order(Query $query, $order_direction)
	{
		$names = $this->app->models['taxonomy.vocabulary']->select('vid, vocabulary')->order("vocabulary " . ($order_direction < 0 ? 'DESC' : 'ASC'))->pairs;

		return $query->order('vid', array_keys($names));
	}

	/**
	 * @param Term $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return new FilterDecorator($record, $this->id, $this->manager->is_filtering($this->id), $record->vocabulary);
	}
}

/**
 * Representation of the `popularity` column.
 *
 * The column displays the times a term is associated to a node.
 */
class PopularityColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options = [])
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
