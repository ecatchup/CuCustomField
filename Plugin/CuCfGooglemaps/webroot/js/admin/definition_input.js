/**
 * CuCustomField : baserCMS Custom Field Googlemaps Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfGooglemaps.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'googlemaps') {
            $("#RowCuCfParentId").hide();
            $("#RowCuCfGoogleMaps").show('slow');
        }
    }
});

