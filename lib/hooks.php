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
use ICanBoogie\Exception;
use ICanBoogie\Operation;

use Icybee\Modules\Nodes\Operation\DeleteOperation;

class Hooks
{
	/**
	 * Deletes the terms associated with a node when the node is deleted.
	 *
	 * @param Operation\ProcessEvent $event
	 * @param DeleteOperation $target
	 */
	static public function on_nodes_delete(Operation\ProcessEvent $event, DeleteOperation $target)
	{
		\ICanBoogie\app()->models['taxonomy.terms/nodes']->filter_by_nid($event->rc)->delete();
	}

	static public function markup_terms(array $args, \Patron\Engine $patron, $template)
	{
		if (isset($args['scope']))
		{
			throw new \Exception('The "scope" parameter is deprecated, use "construtor" instead.');

			$args['constructor'] = $args['scope'];
		}

		$conditions = [];
		$conditions_args = [];

		$inner = ' INNER JOIN {prefix}taxonomy_terms term USING(vid)';

		$constructor = $args['constructor'];

		if ($constructor)
		{
			$inner .= ' INNER JOIN {prefix}taxonomy_vocabulary__scopes USING(vid)';

			$conditions[] = 'constructor = ?';
			$conditions_args[] = $constructor;
		}

		$vocabulary = $args['vocabulary'];

		if ($vocabulary)
		{
			if (is_numeric($vocabulary))
			{
				$conditions[] = 'vid = ?';
				$conditions_args[] = $vocabulary;
			}
			else
			{
				$conditions[] = '(vocabulary = ? OR vocabularyslug = ?)';
				$conditions_args[] = $vocabulary;
				$conditions_args[] = $vocabulary;
			}
		}

		$conditions[] = '(SELECT GROUP_CONCAT(nid) FROM {prefix}taxonomy_terms__nodes tnode
			INNER JOIN {prefix}nodes node USING(nid)
			WHERE is_online = 1 AND tnode.vtid = term.vtid) IS NOT NULL';


		$where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : null;

		/* @var $model TermModel */

		$model = \ICanBoogie\app()->models['taxonomy.terms'];

		$entries = $model->query
		(
			'SELECT voc.*, term.*,

			(SELECT GROUP_CONCAT(nid) FROM {prefix}taxonomy_terms__nodes tnode
			INNER JOIN {prefix}nodes node USING(nid)
			WHERE is_online = 1 AND tnode.vtid = term.vtid
			ORDER BY tnode.weight) AS nodes_ids

			FROM {prefix}taxonomy_vocabulary voc' . $inner . $where . ' ORDER BY term.weight, term',

			$conditions_args
		)
		->fetchAll(\PDO::FETCH_CLASS, Term::class, [ $model ]);

		if ($constructor)
		{
			foreach ($entries as $entry)
			{
				$entry->nodes_constructor = $constructor;
			}
		}

		return $patron($template, $entries);
	}

	/*

	Charge des noeuds 'complets' selon un _vocabulaire_ et/ou une _portée_.

	Parce qu'un même vocabulaire peut-être utilisé sur plusieurs modules, si 'scope' est
	définit le constructeur du noeud doit être connu et égal à 'scope'. Pour cela il nous faut
	joindre la table du module `system.nodes`.

	Si scope est défini c'est plus simple, parce que toutes les entrées sont chargées depuis un
	même module.

	Si scope est défini, il faudrait peut-être modifier 'self' pour qu'il contienne les données du
	terme. Ou alors utiliser un autre marqueur pour l'occasion... hmm ce serait peut-être le mieux.
	<p:taxonomy:term select="" vacabulary="" scope="" />

	Les options de 'range' ne doivent pas être appliquée aux termes mais au noeud chargés dans un
	second temps. Notamment les options d'ordre.

	*/
	static public function markup_nodes(array $args, \Patron\Engine $patron, $template)
	{
		$app = \ICanBoogie\app();

		$term = $patron->context['this'];
		$order = $args['order'];

		if ($term instanceof Term)
		{
			$constructor = $term->nodes_constructor;
			$order = $args['order'] ? strtr($args['order'], ':', ' ') : 'FIELD (nid, ' . $term->nodes_keys . ')';

			$entries = $app->models[$constructor]->where('is_online = 1 AND nid IN(' . $term->nodes_keys . ')')->order($order)->all;

			$taxonomy_property = $term->vocabularyslug;
			$taxonomy_property_slug = $taxonomy_property . 'slug';

			foreach ($entries as $entry)
			{
				$entry->$taxonomy_property = $term;
				$entry->$taxonomy_property_slug = $term->termslug;
			}
		}
		else
		{
			$term = $args['term'];
			$vocabulary = $args['vocabulary'];
			$constructor = $args['constructor'];

			$vocabulary = $app->models['taxonomy.vocabulary']
			->join('INNER JOIN {self}__scopes USING(vid)')
			->join('INNER JOIN {prefix}taxonomy_terms USING(vid)')
			->where('vocabularyslug = ? AND constructor = ? AND termslug = ?', $vocabulary, $constructor, $term)
			->one;

			$patron->context['self']['vocabulary'] = $vocabulary;

			$ids = $app->db->query
			(
				'SELECT nid FROM {prefix}taxonomy_vocabulary voc
				INNER JOIN {prefix}taxonomy_vocabulary__scopes scopes USING(vid)
				INNER JOIN {prefix}taxonomy_terms term USING(vid)
				INNER JOIN {prefix}taxonomy_terms__nodes tnode USING(vtid)
				WHERE constructor = ? AND term.termslug = ?', [

					$constructor, $term

				]
			)
			->fetchAll(\PDO::FETCH_COLUMN);

			if (!$ids)
			{
				return null;
			}

			$limit = $args['limit'];
			$offset = (isset($args['page']) ? $args['page'] : 0) * $limit;

			$arr = $app->models[$constructor]
			->where(array('is_online' => true, 'nid' => $ids))
			->order($order);

			$count = $arr->count;
			$entries = $arr->limit($offset, $limit)->all;

			$patron->context['self']['range'] = [

				'count' => $count,
				'limit' => $limit,
				'page' => isset($args['page']) ? $args['page'] : 0

			];
		}

		return $patron($template, $entries);
	}
}
