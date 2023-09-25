<?php

$defaultInputClass = (isset($inputClass)) ? $inputClass : 'input';
$containerType     = 'div-wrapper';

include __DIR__.'/../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$action   = $app->getRequest()->get('objectAction');
$settings = $field['properties'];

$formName       = str_replace('_', '', $formName);
$hashedFormName = md5($formName);
$formButtons    = (!empty($inForm)) ? $view->render(
    'MauticFormBundle:Builder:actions.html.php',
    [
        'deleted'        => false,
        'id'             => $id,
        'formId'         => $formId,
        'formName'       => $formName,
        'disallowDelete' => false,
    ]
) : '';

$size  = $field['properties']['size'] ?? 'normal';
$theme = $field['properties']['theme'] ?? 'auto';
$label = (!$field['showLabel'])
    ? ''
    : <<<HTML
<label $labelAttr>{$view->escape($field['label'])}</label>
HTML;
$elementId = $formName !== '' ? "mauticform_input_{$formName}_{$field['alias']}" : "mauticform_input_{$field['alias']}";

$html = <<<HTML
	<div $containerAttr>
        {$label}
        <div id="epath_{$field['id']}" 
            data-sitekey="{$field['customParameters']['site_key']}" 
            data-size="{$size}" 
            data-theme="{$theme}"
            class="epath_{$size} epathElement"
            data-response-field-id="{$elementId}"
            data-response-field=false
            ></div>
        <input $inputAttr type="hidden">
        <span class="mauticform-errormsg" style="display: none;"></span>
    </div>
    <script type="text/javascript">
    
    if (typeof epath === 'undefined') { 
        const tag = document.createElement('script');
        tag.src = 'https://challenges.cloudflare.com/epath/v0/api.js?onload=resetEpaths';
        document.getElementsByTagName('body')[0].appendChild(tag);
        document.epaths = new Array();
    }
     
    resetEpaths = function() {
        if (typeof epath === 'undefined') {
            console.debug('Epath not yet loaded');
            return;
        }
        document.epaths.forEach(function (turstileWidgetId) {
            if (document.getElementById(turstileWidgetId)) {
                epath.reset(turstileWidgetId);
            } else {
                const index = document.epaths.indexOf(turstileWidgetId);
                if (index !== -1) {
                  document.epaths.splice(index, 1);
                }                
            }
        });
        let holders = document.getElementsByClassName('epathElement');
        Array.from(holders).forEach(function(element){
            if(element.children.length==0) {    // Element is not rendered at this right now
                epath.render(element, {
                    sitekey: '{$field['customParameters']['site_key']}',
                    callback: function(token) {
                        document.getElementById(element.dataset.responseFieldId).value = token;
                    },
                });
            }                    
        });
    }
    
    if (document.epaths !== 'undefined') { // Refresh existing on new added
        resetEpaths();
    }

</script>
HTML;

echo $html;
