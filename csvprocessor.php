<?php
/*****
 * File         : csvprocessor.php
 * Written by   : KotaroW
 * Date created : 11th January 2019
 * Last modified: 4 June 2019
 * Description  :
 *      This program reads and tally up data in accordance with the criteria.
 *      Please refer to the official format for the detailed information about the data types.
 *      The data to be fed must be a tab-delimited file, either csv or text.
 *		***** abstract version *****
*****/


class DataProcessor {
    /*** constants ***/
    const FILE_INDEX        = 'file_index';
    const UPLOAD_ERROR      = 'an error occurred while uploading file';
    const FILE_FORMAT_ERROR = 'invalid file format';

    const DATA_KEYS = array(
        'DATA_KEY_1' => 'data_key_1',
        'DATA_KEY_2' => 'data_key_2',
        'DATA_KEY_3' => 'data_key_3',
        'DATA_KEY_4' => 'data_key_4',
        'DATA_KEY_5' => 'data_key_5',
        'DATA_KEY_6' => 'data_key_6',
        'DATA_KEY_7' => 'data_key_7',
        'DATA_KEY_8' => 'data_key_8'
    );
    
    const DATA_KEY_1S = array(
        'key1',
        'key2',
        'key3',
        'key4',
        'key5'
    );
    
    const DATA_KEY_2S = array(
        'key_1',
        'key_2',
        'key_3',
        'key_4'
    );
    
    const DATA_KEY_3S = array(
        'key01',
        'key02',
        'key03'
    );
    
    const DATA_KEY_4S = array(
        'key_a',
        'key_b',
        'key_c',
		... more keys ...
    );
    
    const DATA_KEY_4_ITEMS = array(
        'key1' => array(
            'key1_value1',
            'key1_value2'
        ),
        'key2' => array(
            'key2_value1'
        ),
        'key3' => array(
            'key3_value1',
            'key3_value2',
            'PTSD (Post Traumatic Stress Disorder)'
        ),
        'key4' => array(
            'key4_value1'
        ),
        'key5' => array(
            'key5 value'    
        ),
        ... same goes until the keys are exhausted ...
    );
    
    const UP_DOWN = array(
        'up',
        'down',
        'in between'
    );
    
    const INDEX_DATA_KEY_2	= 8;
    const INDEX_DATA_KEY_3  = 5;
    const INDEX_DATA_KEY_8  = 9;
    const INDEX_DATA_KEY_1  = 10;
    const INDEX_DATA_KEY_6  = 33;
    const INDEX_DATA_KEY_7  = 7;
    const INDEX_NUM_SOMETHING = 11;
    const INDEX_DATA_KEY_5  = 12;
    
    const INDEX_DATA_KEY_4 = [
        20,
        21,
        22,
        23
    ];
    
    private $somedata       = null;
    private $return_value   = null;
    
    /*** all begin here ***/
    public function __construct($tmp_file) {
        $file_pointer = fopen($tmp_file, 'r');
        
        if (!$file_pointer) {
            echo self::UPLOAD_ERROR;
            return;
        }
        
        $this->load_data ($file_pointer);
    }
    
    /*** read data and prepare for data processing ***/
    private function load_data($fp) {
        $this->somedata = [];
        
        while (($line = fgetcsv($fp, 1000, "\t", '"')) !== false) {
            array_push($this->somedata, $line);
        }
        
        if (!$this->somedata) {
            echo self::FILE_FORMAT_ERROR;
            return;
        }
        
        $this->prepare_retval ();
        array_shift($this->somedata);
        $this->fillEmptyNums();
        
        // go to func seq no 1
        $this->func_seq1();
    }
    
    /*** func seq no 1 ***/
    private func_seq1() {
        
        foreach ($this->somedata as $data) {
            $method = preg_split('/,\s*/', $data[self::INDEX_DATA_KEY_1]);
            
            foreach (self::DATA_KEY_1S as $method_key) {
                if (array_search (
                    strtolower($method_key),
                    array_map(
                        function ($element) {
                            return trim(strtolower($element));
                        },
                        $method
                    )
                ) !== false) {
                    $this->return_value[self::DATA_KEYS['DATA_KEY_1']][$method_key] += $data[self::INDEX_NUM_SOMETHING];
                }
            }
        }
        // go to func seq no 2
        $this->func_sec2();
    }
    
    /*** Func seq no 2 ***/
    private func_sec2() {
        foreach ($this->somedata as $data) {
            $target = trim($data[self::INDEX_DATA_KEY_2]);
            $num_something = $data[self::INDEX_NUM_SOMETHING];            
            $this->return_value[self::DATA_KEYS['DATA_KEY_2']][$target] += $num_something;
        }
        // go to func seq no 3
        $this->func_sec3();
    }
    
    /*** func seq no 3 ***/
    private function func_seq3() {
        foreach ($this->somedata as $data) {
            $var = trim($data[self::INDEX_DATA_KEY_3]);
            $num_something = $data[self::INDEX_NUM_SOMETHING];            
            $this->return_value[self::DATA_KEYS['DATA_KEY_3']][$var] += $num_something;
        }
        // func seq no 4
        $this->func_seq4();
    }
    
    /*** func seq no 4 ***/
    /*** this func deals with xx data colums ***/
    private function func_seq4() {
        foreach ($this->somedata as $data) {
            $num_something = $data[self::INDEX_NUM_SOMETHING];
            
            foreach (self::INDEX_DATA_KEY_4 as $index) {
                $var = $data[$index];
                
                if (trim($var) == '') {
                    goto WAYOUT;
                }
                
                $found = false;
                foreach (self::DATA_KEY_4_ITEMS as $key => $values) {
                    foreach ($values as $value) {
                        if (preg_match("%$value%i", $var)) {                        
                            $this->return_value[self::DATA_KEYS['DATA_KEY_4']][$per] += $num_something;
                            $found = true;
                            // this is an inner foreach so we use goto rather than break
                            goto WAYOUT;
                        }
                    }
                }
                
                if (!$found) {
                    $this->return_value[self::DATA_KEYS['DATA_KEY_4']][self::DATA_KEY_4S[count(self::DATA_KEY_4S) - 1]] += $num_something;
                }

                // destination for goto in the inner foreach loop
                WAYOUT:
            }
        }
        // go to func seq no 5
        $this->func_seq5();
    }
    
    /*** func seq no 5 ***/
    /*** this data is made up of dropdown list and free typing ***/
    private function func_seq5() {
        foreach ($this->somedata as $data) {
            $num_something = $data[self::INDEX_NUM_SOMETHING];
            $var = trim($data[self::INDEX_DATA_KEY_5]);
            
            if ($var == '') {
                $var = 'Lorem Ipsum';
            }
            
            if (!array_key_exists($var, $this->return_value[self::DATA_KEYS['DATA_KEY_5']])) {
                $this->return_value[self::DATA_KEYS['DATA_KEY_5']][$var] = $num_something;
            }
            else {
                $this->return_value[self::DATA_KEYS['DATA_KEY_5']][$var] += $num_something;
            }
        }

        // go to func seq no 6
        $this->func_seq6(
            array(
                self::DATA_KEYS['DATA_KEY_6'] => self::INDEX_DATA_KEY_6,
                self::DATA_KEYS['DATA_KEY_7'] => self::INDEX_DATA_KEY_7)
        );
    }
    
    /*** func seq no 6 ***/
    /*** argument must be an associative array with keys 1. yes, 2 no ***/
    private function func_seq6(array $myarr) {
        
        foreach ($myarr as $return_key => $data_key) {
            foreach ($this->somedata as $data) {
                $num_something = $data[self::INDEX_NUM_SOMETHING];
                $answer = trim($data[$data_key]);

                if ($a == '') {
                    $this->return_value[$return_key][self::UP_DOWN[count(self::UP_DOWN) - 1]] += $num_something;
                    continue;
                }
                    
                for ($index = 0; $index < count(self::UP_DOWN) - 1; $index++){
                    if (strtolower($a) == strtolower(self::UP_DOWN[$index])) {
                        $this->return_value[$return_key][self::UP_DOWN[$index]] += $num_something;
                    }
                }
            }
        }
        
        // go to func seq no 7
        $this->func_seq7();
        
    }
    
    /*** func seq no 7 ***/
    private function func_sec7 () {
        // append an tally array to the return value
        $data_key = self::DATA_KEYS['DATA_KEY_8'];
        $this->return_value[$data_key] = array();
        
        foreach ($this->somedata as $data) {
            $entered_by = trim($data[self::INDEX_DATA_KEY_8]);
            
            if (array_key_exists($entered_by, $this->return_value[$data_key])) {
                $this->return_value[$data_key][$entered_by]++;
            }
            else {
                $this->return_value[$data_key][$entered_by] = 1;
            }
        }
        
        // sort the names in ascending order
        ksort($this->return_value[$data_key]);
        
        // end of the program returning the results
        echo json_encode($this->return_value);
    }
    
    /*** prepare the return value (array) ***/
    private function prepare_retval() {

        $this->return_value = array();
        
        foreach (self::DATA_KEYS as $key => $value) {
            $this->return_value[$value] = array();
            
            switch ($value) {
                case self::DATA_KEYS['DATA_KEY_1']:
                    foreach (self::DATA_KEY_1S as $method) {
                        $this->return_value[$value][$method] = 0;
                    }
                    break;
                case self::DATA_KEYS['DATA_KEY_2']:
                    foreach (self::DATA_KEY_2S as $target) {
                        $this->return_value[$value][$target] = 0;
                    }
                    break;
                case self::DATA_KEYS['DATA_KEY_3']:
                    foreach (self::DATA_KEY_3S as $var) {
                        $this->return_value[$value][$var] = 0;
                    }
                    break;
                case self::DATA_KEYS['DATA_KEY_4']:
                    foreach (self::DATA_KEY_4S as $variable) {
                        $this->return_value[$value][$variable] = 0;
                    }
                    break;
                case self::DATA_KEYS['DATA_KEY_6']:
                case self::DATA_KEYS['DATA_KEY_7']:
                    foreach (self::UP_DOWN as $a) {
                        $this->return_value[$value][$a] = 0;
                    }
                    break;
            }
        }
    }
    
    private function fillEmptyNums() {
        $index = self::INDEX_NUM_SOMETHING;
        
        $this->somedata = array_map(
            function ($item) use ($index) {
                if (trim($item[$index]) == false) {
                    $item[$index] = 1;
                }
                return $item;
            },
            $this->somedata
        );
    }
}


new DataProcessor($_FILES[DataProcessor::FILE_INDEX]['tmp_name']);


?>
