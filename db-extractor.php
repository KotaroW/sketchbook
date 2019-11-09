<?php

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=ssd_data.csv");
header("Pragma: no-cache");
header("Expires: 0");

error_reporting(E_ALL);

define ('IDENTIFIER', 'identifier');
define ('KEY', 'value');

/* array index for output format */
define('DEFINE_1', 'define_1');
define('DEFINE_2', 'define_2');
define('DEFINE_3', 'define_3');
define('DEFINE_4', 'define_4');
define('DEFINE_6','define_6');
define('DEFINE_5', 'define_5');
define('DEFINE_8', 'define_8');
define('DEFINE_9', 'define_9');
define('DEFINE_10', 'define_10');
define('DEFINE_7', 'define_7');
define('DEFINE_11', 'define_11');
define('DEFINE_12', 'define_12');
define('DEFINE_13', 'define_13');


/* array key for the fields array */
define ('C_DEFINE_9', 'a');
define ('C_DEFINE_7', 'b');
define ('C_DEFINE_5', 'c');
define ('C_DEFINE_8', 'd');
define ('C_DEFINE_10', 'e');
define ('C_DEFINE_11', 'f');
define ('C_DEFINE_6', 'g');


define ('NEWLINE_MARKER', '{{newline}}');


$db = @new mysqli('host', 'user', 'password', 'database');

if ($db->connect_errno) {
    die ("Error while connecting to the DB.");
}

$db->set_charset("utf8");
$query_string = "SELECT `table1`.`field1` as fieldas1, `table1`.`field2` as fieldas2, `table1`.`field3` as fieldas3, `table2`.field1 as fieldas1, `table1`.`field4` as fieldas4, `table1`.`field5` as fieldas5 FROM `table1` inner join `table2` on `table1`.`foreignkey` = `table2`.primarykey where `table1`.`field` = condition and `table1`.`field` = condition order by field asc, field asc, field asc";

$result = $db->query($query_string);

if (!$result) {
    die ('An error occurred. Please try again');
}

$csv_arr = [];
$item_count = 0;

while ($row = $result->fetch_assoc()) {
    $output_format = get_output_format();

    $output_format[DEFINE_1] = $row[DEFINE_1];
    $output_format[DEFINE_2] = $row[DEFINE_2];
    $output_format[DEFINE_3] = $row[DEFINE_3];
    $output_format[DEFINE_4] = $row[DEFINE_4];
    
    $some_fields = json_decode($row['extra_fields'], true);
    $fields = decompose_fields($some_fields);
    
    $output_format[DEFINE_6] = $fields[DEFINE_6];
    $output_format[DEFINE_5] = $fields[DEFINE_5];
    $output_format[DEFINE_8] = $fields[DEFINE_8];
    $output_format[DEFINE_9] = $fields[DEFINE_9];
    $output_format[DEFINE_10] = $fields[DEFINE_10];
    $output_format[DEFINE_7] = $fields[DEFINE_7];
    $output_format[DEFINE_11] = $fields[DEFINE_11];
    $output_format[DEFINE_12] = $fields[DEFINE_12];

    $variable = trim($row['variable']);
    $variable_arr = format_variable($variable);
    
    foreach ($variable_arr as $key => $value) {
        $output_format[DEFINE_13 . strval($key + 1)] = $value;
    }

    $item_count = (count($variable_arr) > $item_count) ? count($variable_arr) : $item_count;
    
    array_push($csv_arr, $output_format);
    
}

/*****
echo '<pre>';
foreach ($csv_arr as $csv_row) {
    print_r($csv_row);
}
echo '</pre>';
*****/

$fp = fopen('php://output', 'w');

$headers = generate_csv_headers($item_count);

fputcsv($fp, $headers, ',', '"');

foreach ($csv_arr as $row) {
    fputcsv($fp, array_values($row), ',', '"');
}

fclose($fp);



$result->free();
$db->close();


function get_output_format() {
    return array(
        DEFINE_1 => null,
        DEFINE_2 => null,
        DEFINE_3 => null,
        DEFINE_4 => null,
        DEFINE_6 => null,
        DEFINE_5 => null,
        DEFINE_8 => null,
        DEFINE_9 => null,
        DEFINE_10 => null,
        DEFINE_7 => null,
        DEFINE_11 => null,
        DEFINE_12 => null
    );
}


function decompose_fields ($some_fields) {
    
    $output = array(
        DEFINE_6 => null',
        DEFINE_5 => null,
        DEFINE_8 => null,
        DEFINE_9 => null,
        DEFINE_10 => null,
        DEFINE_7 => null,
        DEFINE_11 => null,
        DEFINE_12 => null
    );
    
    foreach ($some_fields as $fields) {
        $id = $fields[IDENTIFIER];
        $value = $fields[KEY];
        
        if (strtolower(gettype($value)) == 'string') {
            $value = trim($value);
            
            if (!$value) {
                continue;
            }
            
            if (check_to_see_if($value)) {
                $value = preg_replace('/^https:\/*/', '', $value);
                $output[DEFINE_12] = 'prefix_' . $value;
                continue;
            }
            
        }
        
        
        switch ($id) {
            case C_DEFINE_9;
                $output[DEFINE_9] = 'prefix1_' . $value;
                break;
            case C_DEFINE_7:
                $output[DEFINE_7] = 'prefix2_' . $value;
                break;
            case C_DEFINE_5:
                $output[DEFINE_5] = 'prefix3_' . $value;
                break;
            case C_DEFINE_8:
                $output[DEFINE_8] = $value;
                break;
            case C_DEFINE_10:
                $output[DEFINE_10] = 'prefix4_' . $value;
                break;
            case C_DEFINE_11:
                $var1 = preg_replace('/^https?:\/+/i', '', trim($value[0]));
                $var2 = preg_replace('/^https?:\/+/i', '', trim($value[1]));
                
                if (!$var1 && !$var2) {
                    continue;
                }
                
                $var = $var1 ? $var1 : $var2;
                
                if (check_to_see_if($website)) {
                    preg_match ('/(www\.[^example.com]\S+)/i', $website, $match);
                    if ($match) {
                        $output[DEFINE_11] = 'prefix_' . $match[1];
                    }
                    
                    preg_match ('/(www\.example\.com\S+)/i', $var, $match);
                    if ($match) {
                        $output[DEFINE_12] = 'prefix_' . $match[1];
                    }
                    else {
                        $output[DEFINE_12] = 'prefix_ ' . $var;
                    }
                    
                    continue;
                    
                }
                
                
                $output[DEFINE_11] = 'prefix_' . $var;
                
                break;
            case C_DEFINE_6:
                $values = [associative_array];
                
                foreach ($value as $key => $value) {
                    $value[$key] = $values[$value];
                }
                
                $output[DEFINE_6] = implode(', ', $value);
                break;
        }
    }
    
    return $output;
}

function check_to_see_if($value) {
    preg_match('/needle/i', $value, $match);
    return $match;
}

function format_variable($overview) {
    $patterns = array(
        '/\n|\r|\r\n/s' => '', 
        '/<li>|<dd>/s' => '^ ',
        '/<ul>|<\/ul>/s' => '',
        "/<h4>|<p>|<p\s+class=['\"]\S+['\"]>/s" => '',
        "/<p\s+style=['\"]\w.+\w;['\"]>/s" => '',
        '/<dl>|<\/dl>|<dt>/s' => '',
        '/<strong>|<\/strong>/s' => '',
        '/<div class="page"\s+title="Page\s+\d">(<div\s+class="classname">)?<div\s+class="classname"><div class="classname">/s' => '',
        '/<\/div>/s' => '',
        "/<a href=['\"]\S+['\"]\s*(attr=['\"]\S+['\"]\s*)?(attr=['\"]attrval attrval['\"])?>|<\/a>/s" => '',
        "/<span\s+class=['\"]classname['\"]>|<span\s+class=['\"]s\d['\"]>|<\/span>/s" => '',
        '/<\/h4>|<\/p>|<\/li>|<\/dt>|<\/dd>|<\/o>|<br>|<br\s+\/>/s' => NEWLINE_MARKER
    );
    
    foreach ($patterns as $pattern => $replacement) {
        $overview = preg_replace($pattern, $replacement, $overview);
    }
    
    return preg_split('/' . NEWLINE_MARKER . '/', $overview, -1, PREG_SPLIT_NO_EMPTY);
}

function generate_csv_headers($item_count) {
    $headers = array_keys(get_output_format());
    
    for ($index = 1; $index <= $item_count; $index++) {
        array_push($headers, DEFINE_13 . strval($index));
    }
    
    return $headers;
}


?>
