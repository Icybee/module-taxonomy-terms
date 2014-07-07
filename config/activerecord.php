<?php

namespace Icybee\Modules\Taxonomy\Terms;

return [

	'facets' => [

		'taxonomy.terms' => [

			'vocabulary' => 'Icybee\Modules\Taxonomy\Vocabulary\VocabularyCriterion',
			'scope' => 'Icybee\Modules\Taxonomy\Vocabulary\ScopeCriterion',
			'term' => __NAMESPACE__ . '\TermCriterion'

		]

	]

];