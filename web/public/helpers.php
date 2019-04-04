<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 29.03.19
 * Time: 21:29
 */

/**
 * @param string $str
 * @return string
 */
function variableToName(string $str): string
{
    return ucfirst(str_replace('_', ' ', $str));
}

/**
 * @param array $db_field
 * @return array
 */
function enumTypeDbForPhp(array $db_field): array
{
//    var_dump($db_field);
    $enum = [
        'timestamp' => [
            'type' => 'datetime-local',
            'value' => date('Y-m-d', strtotime($db_field['Value'])) . 'T' . date('H:i', strtotime($db_field['Value'])),
        ],
        'int' => [
            'type' => 'number',
            'min' => 0,
        ],
        'decimal' => [
            'type' => 'number',
            'min' => 0.0,
            'step' => 0.1,
        ],
        'varchar' => [
            'type' => 'text',
        ],
        'tinyint' => [
            'type' => 'checkbox',
            'onclick' => "this.value = +this.checked",
            'checked' => null,
        ],
    ];
    $length = strpos($db_field['Type'], '(');
    $type = substr($db_field['Type'], 0, $length ? $length : strlen($db_field['Type']));
    $result = $enum[$type];
    $result['name'] = $db_field['Field'];
    $result['value'] = $result['value'] ?? $db_field['Value'];
    
    if (!$result['value']) {
        unset($result['checked']);
    }

    if ($db_field['Default'] === null || $db_field['Null'] === 'YES') {
        $result['required'] = true;
    }
    return $result;
}

/**
 * @param array $dataInput
 * @return string
 */
function generatorInput(array $dataInput): string
{
    $result = "<input ";
    foreach ($dataInput as $k => $v) {
        if (is_bool($v) && $v) {
            $result .= "$k ";
        } else {
            $result .= "$k='{$v}' ";
        }
    }
    $result .= '/>';
    return $result;
}