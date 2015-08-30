<?php

namespace Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;

use ICanBoogie\ActiveRecord\Query;
use Icybee\Block\ManageBlock\Column;
use Icybee\Block\ManageBlock\FilterDecorator;
use Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;
use Icybee\Modules\Taxonomy\Terms\Term;

/**
 * Representation of the `vid` column.
 */
class VidColumn extends Column
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
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

