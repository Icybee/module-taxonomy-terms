<?php

namespace Icybee\Modules\Taxonomy\Terms;

use Icybee\Modules\Taxonomy\Vocabulary;

return [

	'facets' => [

		'taxonomy.terms' => [

			'vocabulary' => Vocabulary\VocabularyCriterion::class,
			'scope' => Vocabulary\ScopeCriterion::class,
			'term' => TermCriterion::class,
			'usage' => UsageCriterion::class

		]

	]

];
