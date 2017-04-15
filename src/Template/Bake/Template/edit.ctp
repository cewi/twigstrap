<%
/**
 * This file is part of Twigstrap
 *
 ** (c) 2017 cewi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
 use Cake\Utility\Inflector;

 $fields = collection($fields)
     ->filter(function($field) use ($schema) {
         return $schema->columnType($field) !== 'binary';
     });

 if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
     $fields = $fields->reject(function ($field) {
         return $field === 'lft' || $field === 'rght';
     });
 }
 %>
{{_view.extends('default')}}

{{ _view.start('sidebar') }}
<div class="card">
	<h3 class="card-header">{{ __('Actions') }}</h3>
	<div class="card-block">
		<ul class="nav flex-column">

<% if (strpos($action, 'add') === false): %>
        <li>{{ Form.postLink(
                __('Delete'),
                {'action' : 'delete', 0 : <%= $singularVar %>.<%= $primaryKey[0] %>},
                {'confirm' : __('Are you sure you want to delete # {0}?', <%= $singularVar %>.<%= $primaryKey[0] %>)}
            )|raw
            }}
        </li>
<% endif; %>
        <li>{{ Html.link(__('List <%= $pluralHumanName %>'), {'action' : 'index'})|raw }}</li>
			</ul>
		</div>
	</div>
	{{ _view.end() }} 

<%= $this->element('form'); %>
