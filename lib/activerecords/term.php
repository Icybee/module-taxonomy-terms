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

use ICanBoogie\Routing\ToSlug;

/**
 * A term of a vocabulary.
 *
 * @property-read array $nodes_keys
 */
class Term extends \ICanBoogie\ActiveRecord implements \IteratorAggregate, \Brickrouge\CSSClassNames, ToSlug
{
	use \Brickrouge\CSSClassNamesProperty;

	const VTID = 'vtid';
	const VID = 'vid';
	const TERM = 'term';
	const TERMSLUG = 'termslug';
	const WEIGHT = 'weight';

	/**
	 * Identifier of the vocabulary term.
	 *
	 * @var int
	 */
	public $vtid;

	/**
	 * Identifier of the vocabulary the term belongs to.
	 *
	 * @var int
	 */
	public $vid;

	/**
	 * Name of the term.
	 *
	 * @var string
	 */
	public $term;

	/**
	 * Normalized name of the term.
	 *
	 * @var string
	 */
	public $termslug;

	/**
	 * Weight of the term relative to other terms in the same vocabulary.
	 *
	 * @var int
	 */
	public $weight;

	/**
	 * The `$model` property defaults to "taxonomy.terms".
	 *
	 * @param string $model
	 */
	public function __construct($model='taxonomy.terms')
	{
		parent::__construct($model);
	}

	/**
	 * Returns the {@link $term} property.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->term;
	}

	/**
	 * Returns the iterator for the IteratorAggregate interface.
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->nodes);
	}

	public function to_slug()
	{
		return $this->termslug;
	}

	/**
	 * Returns the vocabulary the term belongs to.
	 *
	 * @return \Icybee\Modules\Taxonomy\Vocabulary\Vocabulary
	 */
	protected function lazy_get_vocabulary()
	{
		return $this->vid ? \ICanBoogie\app()->models['taxonomy.vocabulary'][$this->vid] : null;
	}

	static private $nodes_keys_by_vid_and_vtid = array();

	/**
	 * Returns the nodes keys associated with the term.
	 *
	 * Note: In order to reduce the number of database requests, the nodes keys of _all_ the terms
	 * in the same vocabulary are gathered.
	 *
	 * @return array
	 */
	protected function lazy_get_nodes_keys()
	{
		$vid = $this->vid;

		if (!isset(self::$nodes_keys_by_vid_and_vtid[$vid]))
		{
			$groups = \ICanBoogie\app()->models['taxonomy.terms/nodes']
			->select('vtid, nid')
			->filter_by_vid($this->vid)
			->order('term_node.weight')
			->all(\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP);

			foreach ($groups as &$keys)
			{
				if (empty($keys)) continue;

				$keys = array_combine($keys, $keys);
			}

			unset($keys);

			self::$nodes_keys_by_vid_and_vtid[$vid] = $groups;
		}

		$vtid = $this->vtid;

		if (!isset(self::$nodes_keys_by_vid_and_vtid[$vid][$vtid]))
		{
			return array();
		}

		return self::$nodes_keys_by_vid_and_vtid[$vid][$vtid];
	}

	/**
	 * Returns the nodes associated with the term.
	 *
	 * @return array The nodes associated with the term, or an empty array if there is none.
	 */
	protected function lazy_get_nodes()
	{
		$ids = $this->model
		->select('nid')
		->join('INNER JOIN {prefix}taxonomy_terms__nodes ttnode USING(vtid)') // FIXME-20110614 Query should be cleverer then that
		->join(':nodes')
		->filter_by_vtid($this->vtid)
		->where('is_online = 1')
		->order('ttnode.weight')
		->all(\PDO::FETCH_COLUMN);

		if (!$ids)
		{
			return [];
		}

		$constructors = $this->app->models['nodes']->select('constructor, nid')->where([ 'nid' => $ids ])
		->all(\PDO::FETCH_GROUP | \PDO::FETCH_COLUMN);

		$rc = array_flip($ids);

		foreach ($constructors as $constructor => $constructor_ids)
		{
			$records = $this->app->models[$constructor]->find($constructor_ids);

			foreach ($records as $id => $record)
			{
				$rc[$id] = $record;
			}
		}

		return array_values($rc);
	}

	/**
	 * Returns the CSS class names of the term.
	 *
	 * @return array[string]mixed
	 */
	protected function get_css_class_names()
	{
		$vocabulary_slug = '';

		if (isset($this->vocabularyslug))
		{
			$vocabulary_slug = $this->vocabularyslug;
		}
		else if ($this->vid && $this->vocabulary)
		{
			$vocabulary_slug = $this->vocabulary->vocabularyslug;
		}

		return [

			'type' => 'term',
			'id' => 'term-' . $this->vtid,
			'slug' => 'term-slug--' . $this->termslug,
			'vid' => $this->vid ? 'vocabulary-' . $this->vid : null,
			'vslug' => $vocabulary_slug ? "vocabulary-slug--{$vocabulary_slug}" : null

		];
	}
}
