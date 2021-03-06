<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Taxonomy\Terms\Block;

use Brickrouge\Element;
use Brickrouge\Form;
use Brickrouge\Widget;

use Icybee\Modules\Nodes\TitleSlugCombo;
use Icybee\Modules\Taxonomy\Terms\Term;

/**
 * @property Term $record
 */
class EditBlock extends \Icybee\Block\EditBlock
{
	protected function lazy_get_children()
	{
		$vid = $this->values['vocabulary_id'];

		$vid_options = [ null => '' ] + $this->app->models['taxonomy.vocabulary']
				->select('vocabulary_id, vocabulary')
				->pairs;

		$parent_options = [ null => '' ]
			+ $this->app->models['taxonomy.terms']
				->select('term_id, term')
				->filter_by_vocabulary_id($vid)
				->pairs;

		/*
		 * Beware of the 'weight' property, because vocabulary also define 'weight' and will
		 * override the term's one.
		 */

		return array_merge(parent::lazy_get_children(), [

			Term::TERM => new TitleSlugCombo([

				Form::LABEL => 'Term',
				Element::REQUIRED => true

			]),

			Term::PARENT_ID => new Element('select', [

				Form::LABEL => 'Parent',
				Element::OPTIONS => $parent_options

			]),

			Term::VOCABULARY_ID => new Element('select', [

				Form::LABEL => 'Vocabulary',
				Element::OPTIONS => $vid_options,
				Element::REQUIRED => true

			])
		]);
	}
}
