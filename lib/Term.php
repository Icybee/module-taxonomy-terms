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

use ICanBoogie\ActiveRecord;
use ICanBoogie\Routing\ToSlug;

use Brickrouge\CSSClassNames;
use Brickrouge\CSSClassNamesProperty;

use Icybee\Modules\Nodes\Node;
use Icybee\Modules\Taxonomy\Vocabulary\Vocabulary;

/**
 * A term of a vocabulary.
 *
 * @property Node[] $nodes
 * @property array $nodes_keys
 * @property Vocabulary $vocabulary
 */
class Term extends ActiveRecord implements \IteratorAggregate, CSSClassNames, ToSlug
{
	use CSSClassNamesProperty;

	const MODEL_ID = 'taxonomy.terms';

	const TERM_ID = 'term_id';
	const VID = 'vid';
	const TERM = 'term';
	const TERMSLUG = 'termslug';
	const PARENT_ID = 'parent_id';
	const WEIGHT = 'weight';

	/**
	 * Identifier of the vocabulary term.
	 *
	 * @var int
	 */
	public $term_id;

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
	 * Parent term identifier.
	 *
	 * @var int
	 */
	public $parent_id;

	/**
	 * Weight of the term relative to other terms in the same vocabulary.
	 *
	 * @var int
	 */
	public $weight;

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
	 * @return Vocabulary
	 */
	protected function lazy_get_vocabulary()
	{
		return $this->vid
			? $this->model->models['taxonomy.vocabulary'][$this->vid]
			: null;
	}

	static private $nodes_keys_by_vocabulary_and_term = [];

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

		if (!isset(self::$nodes_keys_by_vocabulary_and_term[$vid]))
		{
			$groups = $this->model->models['taxonomy.terms/nodes']
			->select('term_id, nid')
			->filter_by_vid($this->vid)
			->order('term_node.weight')
			->all(\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP);

			foreach ($groups as &$keys)
			{
				if (empty($keys)) continue;

				$keys = array_combine($keys, $keys);
			}

			unset($keys);

			self::$nodes_keys_by_vocabulary_and_term[$vid] = $groups;
		}

		$term_id = $this->term_id;

		if (!isset(self::$nodes_keys_by_vocabulary_and_term[$vid][$term_id]))
		{
			return array();
		}

		return self::$nodes_keys_by_vocabulary_and_term[$vid][$term_id];
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
		->join('INNER JOIN {prefix}taxonomy_terms__nodes ttnode USING(term_id)') // FIXME-20110614 Query should be cleverer then that
		->join(':nodes')
		->filter_by_vtid($this->term_id)
		->where('is_online = 1')
		->order('ttnode.weight')
		->all(\PDO::FETCH_COLUMN);

		if (!$ids)
		{
			return [];
		}

		$models = $this->model->models;

		$constructors = $models['nodes']
			->select('constructor, nid')
			->where([ 'nid' => $ids ])
			->all(\PDO::FETCH_GROUP | \PDO::FETCH_COLUMN);

		$rc = array_flip($ids);

		foreach ($constructors as $constructor => $constructor_ids)
		{
			$records = $models[$constructor]->find($constructor_ids);

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
			'id' => 'term-' . $this->term_id,
			'slug' => 'term-slug--' . $this->termslug,
			'vid' => $this->vid ? 'vocabulary-' . $this->vid : null,
			'vslug' => $vocabulary_slug ? "vocabulary-slug--{$vocabulary_slug}" : null

		];
	}
}
