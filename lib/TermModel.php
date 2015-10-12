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

use ICanBoogie\ActiveRecord\Model;

class TermModel extends Model
{
	/**
	 * If the `term_slug` property is empty it is created from the `term` property, otherwise
	 * the it is normalized.
	 *
	 * @inheritdoc
	 */
	public function save(array $properties, $key = null, array $options = [])
	{
		if (isset($properties[Term::TERM]) && empty($properties[Term::TERM_SLUG]))
		{
			$properties[Term::TERM_SLUG] = \Icybee\slugize($properties[Term::TERM]);
		}
		else if (isset($properties[Term::TERM_SLUG]))
		{
			$properties[Term::TERM_SLUG] = \ICanBoogie\normalize($properties[Term::TERM_SLUG]);
		}

		return parent::save($properties, $key, $options);
	}

	/**
	 * @param Term[] $records
	 * @param callable $alter_query
	 */
	public function including_usage($records, callable $alter_query = null)
	{
		$ids = [];

		foreach ($records as $record)
		{
			$ids[$record->term_id] = $record;
			$record->usage = null;
		}

		if (!$ids)
		{
			return;
		}

		$query = $this
			->select('term_id, COUNT(nid)')
			->group('term_id')
			->join(':taxonomy.terms/nodes')
			->filter_by_term_id(array_keys($ids));

		if ($alter_query)
		{
			$alter_query($query);
		}

		foreach ($query->pairs as $term_id => $usage)
		{
			$ids[$term_id]->usage = $usage;
		}
	}
}
