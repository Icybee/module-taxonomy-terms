<?php

namespace Icybee\Modules\Taxonomy\Terms;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return [

	Descriptor::CATEGORY => 'organize',
	Descriptor::DESCRIPTION => 'Manage vocabulary terms',

	Descriptor::MODELS => [

		'primary' => [

			Model::IMPLEMENTING => [

				[ 'model' => 'taxonomy.vocabulary/primary' ]

			],

			Model::SCHEMA => [

				'fields' => [

					'vtid' => 'serial',
					'vid' => 'foreign',
					'term' => 'varchar',
					'termslug' => 'varchar',
					'weight' => [ 'integer', 'unsigned' => true ]

				]
			]
		],

		'nodes' => [

			Model::ALIAS => 'term_node',
			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::IMPLEMENTING => [

				[ 'model' => 'taxonomy.terms/primary' ]

			],

			Model::SCHEMA => [

				'fields' => [

					'vtid' => [ 'foreign', 'primary' => true ],
					'nid' => [ 'foreign', 'primary' => true ],
					'weight' => [ 'integer', 'unsigned' => true ]

				]
			]
		]
	],

	Descriptor::NS => __NAMESPACE__,
	Descriptor::REQUIRES => [

		'nodes' => '1.0',
		'taxonomy.vocabulary' => '1.0'

	],

	Descriptor::TITLE => 'Terms'

];
