<?php

namespace Icybee\Modules\Taxonomy\Terms;

use ICanBoogie\HTTP\Request;
use Icybee\Routing\RouteMaker as Make;

return Make::admin('taxonomy.terms', Routing\TermsAdminController::class, [

	'id_name' => 'vtid',
	'only' => [ 'index', 'create', 'edit', 'delete', 'confirm-delete' ]

]);
