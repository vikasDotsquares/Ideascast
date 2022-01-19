<?php

/**
 * Component for Smart Search.
 */
ini_set("max_execution_time", 9999);
set_time_limit(9999);

class SearchComponent extends Component {

    // var $components = array('Html', 'Session', 'Thumbnail');
    private $conn = "";
    private $protectedDatabases = array(
        'information_schema'
    );
    private $allTables = array();
    private $counter = 0;

    /**
     * Contructor Function
     *
     * @access public
     * @param array $searchFromDatabase
     *        	[optional]
     * @return resource
     */
    function init($searchFromDatabase = "") {

        //$this->conn = mysql_pconnect(SEARCH_SERVERNAME, SEARCH_USERNAME, SEARCH_PASSWORD) or die(mysql_error());

		$this->conn = mysqli_connect(SEARCH_SERVERNAME, SEARCH_USERNAME, SEARCH_PASSWORD);
		$searchFromDatabase = '';
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

        if (is_array($searchFromDatabase) && !empty($searchFromDatabase) && count($searchFromDatabase) > 0) {
            $databaseArray = $searchFromDatabase;
        } else {
            $databaseArray = $this->getAllDatabases();
        }
        // $tableArray = $this->getAllTables($databaseArray);

        $tableArray[SEARCH_DB] = [
            'projects',
            'workspaces',
            'elements',
            'element_decisions',
            'element_decision_details',
            'element_documents',
            'element_links',
            'element_notes',
            'feedback',
            'feedback_attachments',
            //'feedback_results',
            'votes',
            'template_relations',
            'do_lists',
            'do_list_comments',
            'do_list_comment_uploads',
            'blogs',
            'blog_comments',
            'blog_documents',
            'wikies',
            'wiki_pages',
            'wiki_page_comments',
            'wiki_page_comment_documents'
        ];

        $this->allTables = $this->getAllFields($tableArray);

    }

    /**
     * Function to extract all databases of mysql
     *
     * @access private
     * @return array
     */
    private function getAllDatabases() {
        $return = array();
        //$db_list = mysql_list_dbs($this->conn);

		$sql="SHOW DATABASES";
		$db_list = mysqli_query($this->conn,$sql);

        while ($row = mysqli_fetch_object($db_list)) {
            if (!in_array($row->Database, $this->protectedDatabases)) {
				$getPrefix = explode("_",$row->Database);
				if( $getPrefix[0].'_' == DOMAINPREFIX ){
					$return [] = $row->Database;
				}
            }
        }

        return $return;
    }

    /**
     * Function to extract all databases and its tables as an array
     *
     * @access private
     * @param array $databaseArray
     * @return array
     */
    private function getAllTables($databaseArray) {
        $return = array();
        $count = ( isset($databaseArray) && !empty($databaseArray) ) ? count($databaseArray) : 0;
        $table = array();
        for ($i = 0; $i < $count; $i ++) {
            $database = $databaseArray [$i];
            $sql = "SHOW TABLES FROM $database";
            //$result = mysql_query($sql);
			$result = mysqli_query($this->conn,$sql);
            $table = array();
            while ($row = @mysqli_fetch_row($result)) {
                $table [] = $row [0];
            }
            $return [$database] = $table;
        }

        return $return;
    }

    /**
     * Function to extract all fields of all tables along withe respective database names
     *
     * @access private
     * @param array $tableArray
     * @return array
     */
    private function getAllFields($tableArray) {
        $return = array();
        foreach ($tableArray as $database => $table) {

            $count = ( isset($table) && !empty($table) ) ? count($table) : 0;
            $tmpTables = array();
            for ($i = 0; $i < $count; $i ++) {

				$conn = mysqli_connect(SEARCH_SERVERNAME, SEARCH_USERNAME, SEARCH_PASSWORD,$database);

				if (mysqli_connect_errno())
				{
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}

                $tableName = $table [$i];
                $sql = "SHOW COLUMNS FROM `$tableName`";
                $result = mysqli_query($conn,$sql) or die("$database->$sql -- " . mysql_error());
                $fieldArray = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    $type = $row ['Type'];
                    $pos = strpos($type, "(");
                    if ($pos !== false) {
                        $type = substr($type, 0, $pos);
                    }
                    $fieldArray [] = array(
                        $row ['Field'],
                        $type
                    );
                }
                $tmpTables [$tableName] = $fieldArray;
            }
            $return [$database] = $tmpTables;
        }
        return $return;
    }

    /**
     * Function to search keyword in all the fields of each table
     *
     * @access public
     * @param array $tableArray
     * @return array
     */
    public function getSearchResults($keyword, $matchWord = true, $caseSenstive = true) {
        $return = array();
        // print_r($this->allTables);
        foreach ($this->allTables as $database => $tableArray) {
            //mysql_select_db($database);
			$conn = mysqli_connect(SEARCH_SERVERNAME, SEARCH_USERNAME, SEARCH_PASSWORD,$database);
			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

            foreach ($tableArray as $tableName => $fieldArray) {
                $count = ( isset($fieldArray) && !empty($fieldArray) ) ? count($fieldArray) : 0;
                $tmp = array();
                foreach ($fieldArray as $fields) {
                    $field = $fields [0];
                    $type = $fields [1];
                    $typeO = $type;
                    if (is_int($keyword)) {
                        $typeOfKeyword = "int";
                    } elseif (is_array($keyword)) {
                        $typeOfKeyword = "array";
                    } elseif (is_bool($keyword)) {
                        $typeOfKeyword = "boolean";
                    } elseif (is_string($keyword)) {
                        $typeOfKeyword = "string";
                    } elseif (is_float($keyword)) {
                        $typeOfKeyword = "float";
                    }
                    if ($type == "int" || $type == "smallint" || $type == "largeint" || $type == "tinyint" || $type == "mediumint" || $type == "boolean") {
                        $type = "int";
                    }
                   if ($type == "varchar" || $type == "char" || $type == "datetime" || $type == "text" || $type == "longtext" || $type == "mediumtext") {
                        $type = "string";
                    }
                    $type = strtolower($type);
                    $typeO = strtolower($typeO);
                    $typeOfKeyword = strtolower($typeOfKeyword);
                    $type = trim($type);
                    $typeOfKeyword = trim($typeOfKeyword);
                    $typeO = trim($typeO);

                    $more = explode(" ", trim($keyword));

					$like = [];
                    $like_any = [];
                    $like_any_or = [];
                    $likes = '';
                    if (isset($more) && !empty($more)) {
                        $i = 0;
                        foreach ($more as $mor) {

                            if ($i == 0) {
                                $like_any [] = "   '% " . $mor . "%' OR  `" . $field . "` like  '%" . $mor . " %'  OR  `" . $field . "` like  '% " . $mor . " %')";
                                $like_any_or [] = "' %" . $mor . " %'";
                            } else {
                                 $like_any [] = " AND (`" . $field . "` like  '% " . $mor . "%' OR  `" . $field . "` like  '%" . $mor . " %')";



								$like_any_or [] = " OR `" . $field . "` like  '% " . $mor . "%'";
								$like_any_or [] = " OR `" . $field . "` like  '%" . $mor . " %'";
                            }
                            $i ++;
                        }

                        $like [] = "  '% " . $keyword . " %'";
                            $like [] =  " OR `" . $field . "` like  '% " . $keyword . "%'";
                            $like [] =  " OR `" . $field . "` like  '%" . $keyword . " %')";
                          //  $like [] =  " OR `" . $field . "` like  '%" . $keyword . "%'";
                    }
                    $likes = implode($like, '');

                    $like_any = implode($like_any, '');
                    $like_any_or = implode($like_any_or, '');




                    if ($type == $typeOfKeyword) {
                        if ($caseSenstive == true && $matchWord == false) {
                            $keyword = strtolower($keyword);
                            $sql = "SELECT * FROM `$tableName` WHERE  ( `$field` LIKE $likes   AND is_search='1'";
							//$sql = "SELECT * FROM `$tableName` WHERE `$field` like $like_any  AND is_search='1'";

                            $data = mysqli_query($conn,$sql);
                            $getData = mysqli_fetch_array($data);
                            $type = 1;
                        } elseif ($caseSenstive == false && $matchWord == true) {
                            $sql = "SELECT * FROM `$tableName` WHERE `$field` like '%$keyword%' OR `$field` like '$keyword%' OR `$field` like '%$keyword'  AND is_search='1'";
                            $data = mysqli_query($conn,$sql);
                            $getData = mysqli_fetch_array($data);
                            $type = 2;
                        } elseif ($caseSenstive == true && $matchWord == true) {
                            $sql = "SELECT * FROM `$tableName` WHERE binary  `$field` LIKE $likes or binary `$field` = '$keyword' or binary `$field` = '$keyword'  AND is_search='1'";
                            $data = mysqli_query($conn,$sql);
                            $getData = mysqli_fetch_array($data);
                            $type = 3;
                        } elseif ($caseSenstive == false && $matchWord == false) {
                            $keyword = strtolower($keyword);
                               $sql = "SELECT * FROM `$tableName` WHERE ( `$field` like  $like_any  AND is_search='1'";

							   //echo $sql."<br>";
                            $type = 4;
                        }

                        $res = mysqli_query($conn,$sql) or die("$sql -- " . mysqli_error());
                        $num = mysqli_num_rows($res);



                        if ($num > 0) {
                            $tmp [] = array(
                                'FieldName' => $field,
                                "Query" => $sql,
                                "keyword" => $keyword,
                                "field" => $field,
                                "type" => $type,
                                "num" => $num
                            );
                        }
                    }
                }
				if( isset($tmp) && !empty($tmp) ){
					if (count($tmp) > 0) {

						$return [$database] [$tableName] = $tmp;

					}
				}
            }


        }

        return $return;
    }

}
