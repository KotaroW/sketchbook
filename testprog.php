<?php
/*****
 * file: testprog.php
 * writen by: KotaroW
 * date created: 9th May, 2019
 * description: just a sketch
*****/

/*** constants ***/
// for request methods
define('METHOD_GET', 'get');
define('METHOD_INSERT', 'insert');
define('METHOD_UPDATE', 'update');
define('METHOD_DELETE', 'delete');

// for table name
define('TABLE_ONE', 'table_one');
define('TABLE_TWO', 'table_two');
define('TABLE_THREE', 'table_three');

// for table *** ****
define('field_one', 'field1');
define('field_two', 'field2');
define('field_three', 'field3');

// db transaction code
define('DATA_TRANSACTION_OK', 1);
define('DATA_RETRIEVAL_ERR', -1);
define('DATA_WRITE_ERR', -1);


/*** Connect to Database ***/
$db = @new mysqli('host', 'user', 'password', 'database');

/*** any connection errors terminate the script ***/
if ($db->connect_errno) {
    die ("DB CONNECTION ERROR - Lorem Ipsum ...");
}

/*** determine the request as get or post (insert, update or delete) ***/
$request_method = strtolower($_SERVER['REQUEST_METHOD']);

if ($request_method == METHOD_GET) {
    $table = $_GET['query_param'];
    $query_string = null;
    
    switch ($table) {
        case TABLE_ONE:
            $field1 = $_GET[field_one];
            $field2 = $_GET[field_two];
            $query_string = sprintf(
                'select * from %s where %s = %s and %s = %s order by %s;',
                TABLE_ONE,
                '`' . field_one . '`',
                strval($field1),
                field_two,
                strval($field_two),
                field_three
            );
            break;
            
        case TABLE_TWO:
            $query_string = sprintf(
                'select * from %s order by %s;',
                TABLE_TWO,
                'field'
            );
            break;
            
        case TABLE_THREE:
            $query_string = sprintf(
                'select * from %s order by %s;',
                TABLE_THREE,
                'field'
            );
            break;
    }
    
    $result = $db->query($query_string);    
    $db->close();
    
    if ($result) {
        echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
    }
    else {
        echo DATA_RETRIEVAL_ERR;
    }
    
}
else {
    $request_method = htmlspecialchars($_POST['method']);
    // after extract the method, the key/value pair is no longer needed
    array_shift($_POST);
    $query_string = null;

    foreach ($_POST as $key => $value) {
        if ($value && gettype($value) == 'string') {
            //$_POST[$key] = $db->real_escape_string($value);
        }   
    }
 
    switch ($request_method) {
        case METHOD_INSERT:
            // id field not needed
            array_shift($_POST);
            
            // extract and format table fields first
            $fields = array_keys($_POST);
            $fields = array_map(
                function ($field) {
                    $field = str_replace('-', '_', $field);
                    
                    if ($field == field_one) {
                        $field = '`' . $field . '`';
                    }
                    
                    return $field;
                },
                $fields
            );
            
            // then values
            $values = array_values($_POST);
            $values = array_map(
                function ($value) {
                    $value = '"' . strval($value) . '"';
                    
                    return $value;
                },
                $values
            );
            
            $query_string =  sprintf('insert into %s (%s) values (%s);', TABLE_ONE, implode(',', $fields), implode(',', $values));

            break;
        
        case METHOD_UPDATE:
            
            $id = array_shift($_POST);
            $fields = array_keys($_POST);
            $values = array_values($_POST);
            $values = array_map(
                function ($value) {
                    return '"' . strval($value) . '"';
                },
                $values
            );
            
            $update_values = [];
            
            for ($index = 0; $index < count($fields); $index++) {
                array_push ($update_values, $fields[$index] . '=' . $values[$index]);
            }
            
            $query_string = sprintf(
                'update %s set %s where id = %d;',
                TABLE_ONE,
                implode(',', $update_values),
                $id
            );
            break;
        
        case METHOD_DELETE:
            $query_string = sprintf('delete from %s where id = %d', TABLE_ONE, $_POST['id']);
            break;
    }

    $db->query($query_string);
    $affected_rows = $db->affected_rows;
//echo $db->error;
    $db->close();
    
    echo $affected_rows;

}
