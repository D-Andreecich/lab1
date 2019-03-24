<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */


function variableToName(string $str): string
{
    return ucfirst(str_replace('_', ' ', $str));
}

function enumTypeDbForPhp(array $db_field, bool $revers = false): array
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
    ];
    $length = strpos($db_field['Type'], '(');
    $type = substr($db_field['Type'], 0, $length ? $length : strlen($db_field['Type']));

    $result = $enum[$type];

    $result['name'] = $db_field['Field'];
    $result['value'] = $result['value'] ?? $db_field['Value'];
    $result['required'] = $db_field['Null'] === 'YES';

    return $result;
}

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