<%
/**
* This file is part of Twigstrap.
*
** (c) 2017 cewi
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
use Cake\Utility\Inflector;

$fields = collection($fields)
->filter(function($field) use ($schema) {
return !in_array($schema->columnType($field), ['binary', 'text']);
});

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
$fields = $fields->reject(function ($field) {
return $field === 'lft' || $field === 'rght';
});
}

if (!empty($indexColumns)) {
$fields = $fields->take($indexColumns);
}

%>

{{_view.extends('default')}}

{{ _view.start('sidebar') }}
<div class="card">
	<h3 class="card-header">{{ __('Actions') }}</h3>
	<div class="card-block">
		<ul class="nav flex-column">
			<li class="nav-item">{{ Html.link(__('New <%= $singularHumanName %>'), {'action' : 'add'}, {'class':'nav-link'})|raw }}</li>
			<% $done = [];
			foreach ($associations as $type => $data):
			foreach ($data as $alias => $details):
			if (!empty($details['navLink']) && $details['controller'] !== $this->name && !in_array($details['controller'], $done)): %>
			<li class="nav-item">{{ Html.link(__('List <%= $this->_pluralHumanName($alias) %>'), {'controller' : '<%= $details['controller'] %>', 'action' : 'index'}, {'class':'nav-link'})|raw }}</li>
			<% $done[] = $details['controller']; endif; endforeach; endforeach; %>
		</ul>
	</div>
</div>
{{ _view.end() }}

<div class="<%= $pluralVar %> index">
	<table class="table table-striped">
		<thead>
			<tr>
				<% foreach ($fields as $field): %>
				<th>{{ Paginator.sort('<%= $field %>')|raw }}</th>
				<% endforeach; %>
				<th class="actions">{{ __('Actions') }}</th>
			</tr>
		</thead>
		<tbody>
			{% for <%= $singularVar %> in <%= $pluralVar %> %}
			<tr>
				<% foreach ($fields as $field) { $isKey = false; if (!empty($associations['BelongsTo'])) { foreach ($associations['BelongsTo'] as $alias => $details) { if ($field === $details['foreignKey']) { $isKey = true; %> 
					<td>
						{{ <%= $singularVar %>.has('<%= $details['property'] %>') ? Html.link(<%= $singularVar %>.<%= $details['property'] %>.<%= $details['displayField'] %>, {'controller' : '<%= $details['controller'] %>', 'action' : 'view', 0 : <%= $singularVar %>.<%= $details['property'] %>.<%= $details['primaryKey'][0] %>})|raw : '' }}
					</td> 
					<% break; } } } if ($isKey !== true) { if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) { %>
						<td>{{ <%= $singularVar %>.<%= $field %> }}</td>
						 <% } else { %>
<td>{{ Number.format(<%= $singularVar %>.<%= $field %>) }}</td> 
<% } } }
                        $pk = $singularVar . '.' . $primaryKey[0]; %>
                    <td class="actions">
						<div class="btn-group" role="group" aria-label="actions">
                        {{ Html.link('<i class="fa fa-eye" aria-hidden="true"></i>', {'action' : 'view', 0 : <%= $pk %>}, {'escape':false, 'title':__('view'), 'class':'btn btn-secondary', 'aria-disabled':true})|raw}}
                        {{ Html.link('<i class="fa fa-pencil" aria-hidden="true"></i>', {'action' : 'edit', 0 : <%= $pk %>}, {'escape':false, 'title':__('edit'), 'class':'btn btn-secondary', 'aria-disabled':true})|raw}}
                        {{ Form.postLink('<i class="fa fa-trash" aria-hidden="true"></i>', {'action' : 'delete', 0 : <%= $pk %>}, {'escape':false, 'title':__('edit'), 'class':'btn btn-danger', 'aria-disabled':true,'confirm' : __('Are you sure you want to delete # {0}?', <%= $pk %>)})|raw }}
					</div>
					</td>
                </tr>
                {% endfor %}
            </tbody>
        </table>
       {% element 'Twigstrap.Paginator/default' %}
    </div>