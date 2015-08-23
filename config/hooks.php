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

$hooks = Hooks::class . '::';

return [

	'patron.markups' => [

		'taxonomy:terms' => [

			$hooks . 'markup_terms', [

				'vocabulary' => null,
				'constructor' => null

			]
		],

		'taxonomy:nodes' => [

			$hooks . 'markup_nodes', [

				'vocabulary' => null,
				'scope' => null,
				'term' => null,

				'by' => 'title',
				'order' => 'asc',
				'limit' => null

			]
		]
	]
];
