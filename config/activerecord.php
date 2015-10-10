<?php

namespace Icybee\Modules\Taxonomy\Terms\Facets;

use Icybee\Modules\Taxonomy\Vocabulary;

return [

	'facets' => [

		'taxonomy.terms' => [

			'vocabulary' => Vocabulary\Facets\VocabularyCriterion::class,
			'vid' => VidCriterion::class,
			'scope' => Vocabulary\Facets\ScopeCriterion::class,
			'term' => TermCriterion::class,
			'usage' => UsageCriterion::class

		]

	]

];
