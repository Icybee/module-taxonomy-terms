<?php

namespace Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;

use ICanBoogie\ActiveRecord\Query;
use Icybee\Block\ManageBlock\Column;
use Icybee\Block\ManageBlock\CriterionColumn;
use Icybee\Block\ManageBlock\CriterionColumnTrait;
use Icybee\Block\ManageBlock\FilterDecorator;
use Icybee\Modules\Taxonomy\Terms\Block\ManageBlock;
use Icybee\Modules\Taxonomy\Terms\Term;

/**
 * Representation of the `vocabulary_id` column.
 */
class VidColumn extends Column implements CriterionColumn
{
	use CriterionColumnTrait;

	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

			'title' => 'Vocabulary',
			'orderable' => true

		]);
	}

	/**
	 * Extends the "vocabulary_id" column by providing vocabulary filters.
	 */
	protected function get_options()
	{
		// Move this to render_header() when it's actually used

		$keys = $this->manager->module->model->select('DISTINCT vocabulary_id')->all(\PDO::FETCH_COLUMN);

		if (count($keys) < 2)
		{
			$this->orderable = false;

			return null;
		}

		// /

		return $this->app->models['taxonomy.vocabulary']
			->select('CONCAT("?vocabulary_id=", vocabulary_id), vocabulary')
			->where([ 'vocabulary_id' => $keys ])
			->order('vocabulary')
			->pairs;
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
