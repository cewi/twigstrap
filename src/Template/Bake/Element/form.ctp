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

<div class="<%= $pluralVar %> form">
    {{ Form.create(<%= $singularVar %>)|raw }}
    <fieldset>
        <legend>{{ __('<%= Inflector::humanize($action) %> {0}', [__('<%= $singularHumanName %>')]) }}</legend>
<%
        foreach ($fields as $field) {
            if (in_array($field, $primaryKey)) {
                continue;
            }
            if (isset($keyFields[$field])) {
                $fieldData = $schema->column($field);
                if (!empty($fieldData['null'])) {
%>
        {{ Form.input('<%= $field %>', {'options' : <%= $keyFields[$field] %>, 'empty' : true})|raw }}
<%
                } else {
%>
        {{ Form.input('<%= $field %>', {'options' : <%= $keyFields[$field] %>})|raw }}
<%
                }
                continue;
            }
            if (!in_array($field, ['created', 'modified', 'updated'])) {
                $fieldData = $schema->column($field);
                if (in_array($fieldData['type'], ['date', 'datetime', 'time']) && (!empty($fieldData['null']))) {
%>
        {{ Form.input('<%= $field %>', {'empty' : true})|raw }}
<%
                } else {
%>
        {{ Form.input('<%= $field %>')|raw }}
<%
                }
            }
        }
        if (!empty($associations['BelongsToMany'])) {
            foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
%>
        {{ Form.input('<%= $assocData['property'] %>._ids', {'options' : <%= $assocData['variable'] %>})|raw }}
<%
            }
        }
%>
    </fieldset>
    {{ Form.button(__('Submit'))|raw }}
    {{ Form.end()|raw }}
</div>