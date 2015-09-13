<?php

namespace Icybee\Modules\Taxonomy\Terms;

use Icybee\Modules\Taxonomy\Vocabulary;

return [

	'facets' => [

		'taxonomy.terms' => [

			'vocabulary' => Vocabulary\Facets\VocabularyCriterion::class,
			'scope' => Vocabulary\Facets\ScopeCriterion::class,
			'term' => TermCriterion::class,
			'usage' => UsageCriterion::class

		]

	]

];
