<?php
/********************************************************
 * file: dbtest.php
 * written by: KotaroW
 * date: 5th Nov, 2019
 * description:
 *     This is a test class and abstracted version.
 ********************************************************/

// this should be called by the caller file.
header("Content-Type:application/json");

// for the testing purpose
error_reporting ("E_ALL");

// class definition. 
class TestClass {
    
    /* constants */
    /******
    This part could be packaged into an abstract class with a header guard (and namespace).
    ******/
    // system errors
    const DB_CONNECT_ERR		= "error (1)";
    const DB_QUERY_ERR1			= "error(2)";
    const DB_SET_CHARSET_ERR	= "error(3)";
    // and other errors ....
    
    /* data keys, index etc */
    // *** IMORTANT: Lorem Ipsum... ***
    const FIELDS = [
        'field 0,
        'field 1',
        'field 2',
        'field 3',
        'field 4',
        'field 5',
        'field 6',
        'field 7'
    ];

    const KEY_ONE		= 'key one';
    const KEY_TWO       = 'key two';
    const KEY_THREE     = 'key three';
    const KEY_FOUR      = 'key four';
    const KEY_FIVE      = 'key five';
    const KEY_SIX       = 'key six';
    const KEY_SEVEN     = 'key seven';
    const KEY_EIGHT     = 'key eight';
    const KEY_NINE      = 'key nine';
    const KEY_TEN       = 'key ten';
    const KEY_ELEVEN    = 'key eleven';
    const KEY_TWELVE    = "key twelve";
    const KEY_THIRTEEN  = 'key thirteen';
    
    // *** Lorem Ipsum
    const INDEX_ONE   = 1;
    const INDEX_TWO   = 2;
    const INDEX_THREE = 3;
    const INDEX_FOUR  = 4;
    const INDEX_FIVE  = 5;
    const INDEX_SIX   = 6;
    const INDEX_SEVEN = 7;

    const PREFIX      = 'prefix_';

    const MY_CONST = [
        'val0',
        'val1',
        'val2',
        'val3',
        'val4'
    ];
    
    // obviously this is a database connection
    private $connection = null;

    // data transporter
    private $data = array(self::KEY_TWO => null, self::KEY_NINE => null);

    // for the later PHP versions use "private const"
    // just don't want these ones to be seen from the outside.
    private $query_one = 'select `field1`, `field2`, `field3` from table_name where condition;';
    private $query_two = 'select field1, field2, field3, field4, field5 from table_name where condition1 and condition2;';


    /* data getter */
    /* return type: array (assoc) */
    private function private_get_data ($data_key) {
        $data = array();
        $query_string = ($data_key == self::KEY_TWO) ? $this->query_one : $this->query_two;
        
        $result = $this->connection->query($query_string);
        
        if (!$result) {
            die (self::DB_QUERY_ERR1);
        }

        if ($data_key == self::KEY_TWO) {
            while ($row = $result->fetch_assoc()) {
                // this str_replace is a redundancy....
                $category_name = str_replace("\\", "", $row[self::KEY_EIGHT]);
                $data[$category_name] = array(
                    self::KEY_SEVEN => $row[self::KEY_SEVEN],
                    self::KEY_TEN => $row[self::KEY_TEN]
                );
            }
        }
        else {
            while ($row = $result->fetch_assoc()) {
                // data_key serves as the key for faster access on the client side
                $data_key = self::PREFIX . $row[self::KEY_SEVEN];
                // be aware of the different key is used
                $data[$data_key][self::KEY_EIGHT] = $row[self::KEY_THIRTEEN];

                $data[$data_key][self::KEY_ONE] = $row[self::KEY_ONE];

                // $exception is acutally in JSON format and we need to decompose and process.
                $exception = $this->process_exceptional_fields($row[self::KEY_FIVE]);
                $data[$data_key][self::KEY_THREE] = $exception[self::KEY_THREE];
                $data[$data_key][self::KEY_TWELVE] = $exception[self::KEY_TWELVE];

                $data[$data_key][self::KEY_FOUR] = $row[self::KEY_SIX];

            }
        }
        
        $result->free();
        
        return $data;
    }
    
    // exception field data processor
    private function process_exceptional_fields($fields) {
        $processed = array(self::KEY_THREE => array(), self::KEY_TWELVE => array());
        
        try {
            $fields = json_decode($fields, true);
        }
        catch (Exception $e) {
            return $processed;
        }

        foreach ($fields as $field) {
            $field_key = $field[self::KEY_SEVEN];
            $field_value = null;
            
            switch ($field_key) {
                case self::INDEX_ONE:
                case self::INDEX_TWO:
                case self::INDEX_THREE:
                case self::INDEX_FOUR:
                case self::INDEX_FIVE:
                case self::INDEX_SIX:
                    if ($field_key != self::INDEX_SIX) {
                        $field_value = trim($field['value']);
                    }
                    else {
                        $field_value = !empty($field['key']['key']) ? trim($field['key']['key']) : '';
                    }
                    
                    if ($field_value) {
                        $processed[self::KEY_THREE][self::FIELDS[$field_key]] = $field_value;
                    }    
                    
                    break;
                case self::INDEX_SEVEN:
                    $field_value = [];
                    if (is_array($field['value'])) {
                        $field_value = array_map(function ($array_index) {
                            return self::MY_CONST[$array_index];
                            
                        },
                        $field['value']);
                        
                        if ($field_value) {
                            $processed[self::KEY_ELEVENS_SERVED] = $field_value;
                        }
                    }
                    break;
            }
        }
 
        return $processed;
    }
   
    
    public function __construct($host, $user, $password, $db) {

        $this->connection = new mysqli($host, $user, $password, $db);
        
        // you will be able to figure out what might have happened by checking the error code
        if ($this->connection->connect_errno) {
            die (self::DB_CONNECT_ERR);
        }

        if (!$this->connection->set_charset("utf8")) {
            die (self::DB_SET_CHARSET_ERR);
        }
    }

    public function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function __get($name) {
        return $this->$name;
    }

    public function public_get_data () {
        $this->data[self::KEY_ELEVEN] = array_slice(self::MY_CONST, 1);
        $this->data[self::KEY_TWO] = $this->private_get_data(self::KEY_TWO);
        $this->data[self::KEY_NINE] = $this->private_get_data(self::KEY_NINE);
        
        return json_encode ($this->data, true);
    }

}
