<?php
declare(strict_types=1);

namespace CuCustomField\Model\Entity;

use Cake\ORM\Entity;

/**
 * CuCustomField Entity
 *
 * @property int $id
 * @property int|null $config_id
 * @property int|null $parent_id
 * @property int|null $lft
 * @property int|null $rght
 * @property string|null $name
 * @property string|null $label_name
 * @property string|null $field_name
 * @property string|null $field_type
 * @property string|null $preview_pref_list
 * @property bool|null $status
 * @property bool|null $required
 * @property string|null $default_value
 * @property string|null $validate
 * @property string|null $validate_regex
 * @property string|null $validate_regex_message
 * @property int|null $size
 * @property int|null $max_length
 * @property int|null $counter
 * @property string|null $placeholder
 * @property int|null $rows
 * @property int|null $cols
 * @property string|null $editor_tool_type
 * @property string|null $choices
 * @property string|null $separator
 * @property bool|null $auto_convert
 * @property float|null $google_maps_latitude
 * @property float|null $google_maps_longitude
 * @property int|null $google_maps_zoom
 * @property string|null $google_maps_text
 * @property string|null $prepend
 * @property string|null $append
 * @property string|null $description
 * @property string|null $option_meta
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 */
class CuCustomFieldDefinition extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'id' => true,
        'config_id' => true,
        'parent_id' => true,
        'lft' => true,
        'rght' => true,
        'name' => true,
        'label_name' => true,
        'field_name' => true,
        'field_type' => true,
        'preview_pref_list' => true,
        'status' => true,
        'required' => true,
        'default_value' => true,
        'validate' => true,
        'validate_regex' => true,
        'validate_regex_message' => true,
        'size' => true,
        'max_length' => true,
        'counter' => true,
        'placeholder' => true,
        'rows' => true,
        'cols' => true,
        'editor_tool_type' => true,
        'choices' => true,
        'separator' => true,
        'auto_convert' => true,
        'google_maps_latitude' => true,
        'google_maps_longitude' => true,
        'google_maps_zoom' => true,
        'google_maps_text' => true,
        'prepend' => true,
        'append' => true,
        'description' => true,
        'option_meta' => true,
        'modified' => true,
        'created' => true,
    ];
}
