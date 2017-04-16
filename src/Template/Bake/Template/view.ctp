<%
/**
* This file is part of Twigstrap.
*
* (c) 2017 cewi
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->columnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "$singularVar.{$primaryKey[0]}";
%>

{{_view.extends('default')}}

{{ _view.start('sidebar') }}
<div class="card">
	<h3 class="card-header">{{ __('Actions') }}</h3>
	<div class="card-block">
		<ul class="nav flex-column">
        <li class="nav-item">{{ Html.link(__('Edit <%= $singularHumanName %>'), {'action' : 'edit', 0 : <%= $pk %>})|raw }}</li>
        <li class="nav-item">{{ Form.postLink(__('Delete <%= $singularHumanName %>'), {'action' : 'delete', 0 : <%= $pk %>}, {'confirm' : __('Are you sure you want to delete # {0}?', <%= $pk %>)})|raw }}</li>
        <li class="nav-item">{{ Html.link(__('List <%= $pluralHumanName %>'), {'action' : 'index'})|raw }}</li>
        <li class="nav-item">{{ Html.link(__('New <%= $singularHumanName %>'), {'action' : 'add'})|raw }}</li>
<%
    $done = [];
    foreach ($associations as $type => $data) {
        foreach ($data as $alias => $details) {
            if ($details['controller'] !== $this->name && !in_array($details['controller'], $done)) {
%>
        <li class="nav-item">{{ Html.link(__('List <%= $this->_pluralHumanName($alias) %>'), {'controller' : '<%= $details['controller'] %>', 'action' : 'index'})|raw }}</li>
        <%
                $done[] = $details['controller'];
            }
        }
    }
%>
		</ul>
	</div>
</div>
{{ _view.end() }}
	
<div class="<%= $pluralVar %> view">
    <h3>{{ <%= $singularVar %>.<%= $displayField %>|h }}</h3>
       <table class="table table-striped">
   <% if ($groupedFields['string']) : %>
   <% foreach ($groupedFields['string'] as $field) : %>
           <tr>
   <%
   if (isset($associationFields[$field])) :
   $details = $associationFields[$field];
   %>
               <td>{{ __('<%= Inflector::humanize($details['property']) %>') }}</td>
               <td>{{ <%= $singularVar %>.has('<%= $details['property'] %>') ? Html.link( <%= $singularVar %>-><%= $details['property'] %>.<%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $singularVar %>.<%= $details['property'] %>.<%= $details['primaryKey'][0] %>]) : '' }}</td>
   <% else : %>
               <td>{{ __('<%= Inflector::humanize($field) %>') }}</td>
               <td>{{ <%= $singularVar %>.<%= $field %> }}</td>
   <% endif; %>
           </tr>
   <% endforeach; %>
   <% endif; %>
   <% if ($groupedFields['number']) : %>
   <% foreach ($groupedFields['number'] as $field) : %>
           <tr>
               <td>{{ __('<%= Inflector::humanize($field) %>') }}</td>
               <td>{{ Number.format(<%= $singularVar %>.<%= $field %>) }}</td>
           </tr>
   <% endforeach; %>
   <% endif; %>
   <% if ($groupedFields['date']) : %>
   <% foreach ($groupedFields['date'] as $field) : %>
           <tr>
               <td>{{ __('<%= Inflector::humanize($field) %>') }}</td>
               <td>{{ <%= $singularVar %>.<%= $field %> }}</td>
           </tr>
   <% endforeach; %>
   <% endif; %>
   <% if ($groupedFields['boolean']) : %>
   <% foreach ($groupedFields['boolean'] as $field) : %>
           <tr>
               <td>{{ __('<%= Inflector::humanize($field) %>') }}</td>
               <td>{{ <%= $singularVar %>.<%= $field %> ? __('Yes') : __('No'); }}</td>
           </tr>
   <% endforeach; %>
   <% endif; %>
   <% if ($groupedFields['text']) : %>
   <% foreach ($groupedFields['text'] as $field) : %>
           <tr>
               <td>{{ __('<%= Inflector::humanize($field) %>') }}</td>
               <td>{{ Text.autoParagraph( <%= $singularVar %>.<%= $field %>) }}</td>
           </tr>
   <% endforeach; %>
   <% endif; %>
       </table>
   </div>
<%
$relations = $associations['HasMany'] + $associations['BelongsToMany'];
foreach ($relations as $alias => $details):
    $otherSingularVar = Inflector::variable($alias);
    $otherPluralHumanName = Inflector::humanize(Inflector::underscore($details['controller']));
    %>
    <div class="related">
        <h4>{{ __('Related <%= $otherPluralHumanName %>') }}</h4>
        {% if (<%= $singularVar %>.<%= $details['property'] %> is not empty) %}
        <table class="table table-striped">
			<thead class="thead-inverse">
            <tr>
<% foreach ($details['fields'] as $field): %>
                <th>{{ __('<%= Inflector::humanize($field) %>') }}</th>
<% endforeach; %>
                <th class="actions">{{ __('Actions') }}</th>
            </tr>
		</thead>
		<tbody>
            {% for <%= $otherSingularVar %> in <%= $singularVar %>.<%= $details['property'] %> %}
            <tr>
            <%- foreach ($details['fields'] as $field): %>
                <td>{{ <%= $otherSingularVar %>.<%= $field %>|h }}</td>
            <%- endforeach; %>
            <%- $otherPk = "{$otherSingularVar}.{$details['primaryKey'][0]}"; %>
            <td class="actions">
				<div class="btn-group" role="group" aria-label="actions">
                {{ Html.link('<i class="fa fa-eye" aria-hidden="true"></i>', {'controller' : '<%= $details['controller'] %>', 'action' : 'view', 0 : <%= $otherPk %>}, {'escape':false, 'title':__('view'), 'class':'btn btn-secondary', 'aria-disabled':true})|raw}}
                {{ Html.link('<i class="fa fa-pencil" aria-hidden="true"></i>', {'controller' : '<%= $details['controller'] %>', 'action' : 'edit', 0 : <%= $otherPk %>}, {'escape':false, 'title':__('edit'), 'class':'btn btn-secondary', 'aria-disabled':true})|raw}}
                {{ Form.postLink('<i class="fa fa-trash" aria-hidden="true"></i>',  {'controller' : '<%= $details['controller'] %>', 'action' : 'delete', 0 : <%= $otherPk %>}, {'escape':false, 'title':__('delete'), 'class':'btn btn-danger', 'aria-disabled':true,'confirm' : __('Are you sure you want to delete # {0}?', <%= $otherPk %>)})|raw }}
			</div>
			</td>
            </tr>
            {% endfor %}
		</tbody>	
        </table>
        {% endif %}
    </div>
<% endforeach; %>
</div>