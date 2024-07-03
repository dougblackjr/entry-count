<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Entry_count
{
    public $return_data = "";

    public function __construct()
    {
        // Fetch the tagdata
        $tagdata = ee()->TMPL->tagdata;
            
            // Fetch params
        $weblog = ee()->TMPL->fetch_param('weblog') ? ee()->TMPL->fetch_param('weblog') : ee()->TMPL->fetch_param('channel');
        $categoryid = ee()->TMPL->fetch_param('category');
        $category_group = ee()->TMPL->fetch_param('category_group');
        $author_group = ee()->TMPL->fetch_param('author_group');
        $urltitle = ee()->TMPL->fetch_param('url_title');
        $entryid = ee()->TMPL->fetch_param('entry_id');
        $site = ee()->TMPL->fetch_param('site');
        $status = ee()->TMPL->fetch_param('status');
        $invalid_input = ee()->TMPL->fetch_param('invalid_input');
        $show_expired = ee()->TMPL->fetch_param('show_expired');
        $author_id = ee()->TMPL->fetch_param('author_id');
        $field_name = ee()->TMPL->fetch_param('field_name');
        $field_value = ee()->TMPL->fetch_param('field_value');
        $field = ee()->TMPL->fetch_param('field') ? ee()->TMPL->fetch_param('field') : 'include';
        $show_future_entries = ee()->TMPL->fetch_param('show_future_entries') == 'yes' ? true : false;
        $start_on_relative = is_numeric(ee()->TMPL->fetch_param('start_on_relative')) ? ee()->TMPL->fetch_param('start_on_relative') : false;
        $stop_before_relative = is_numeric(ee()->TMPL->fetch_param('stop_before_relative')) ? ee()->TMPL->fetch_param('stop_before_relative') : false;
        $start_on = ee()->TMPL->fetch_param('start_on');
        $stop_before = ee()->TMPL->fetch_param('stop_before');
        $month = ee()->TMPL->fetch_param('month');
        $year = ee()->TMPL->fetch_param('year');
        
        // Define variables
        $categoryidclause = '';
        $category_group_clause = '';
        $author_group_clause = '';
        $weblogclause = '';
        $urltitleclause = '';
        $entryidclause = '';
        $siteclause = '';
        $siteclause2 = '';
        $statusclause = '';
        $entryidequalityclause = '';
        $groupbyclause = '';
        $havingclause = '';
        $authoridclause = '';
        $categorytablesclause = '';
        $expiredentriesclause = '';
        $futureentriesclause = '';
        $start_on_relative_clause = '';
        $stop_before_relative_clause = '';
        $start_on_clause = '';
        $stop_before_clause = '';
        $fieldclause = '';
        $monthClause = '';
        $yearClause = '';
        $distinctoperator = '';
        $found_invalid = false;
        $entriesnumber = 0;
        
        // Simple validation of params values
        $invalidchars = array('~', '#', '*', '{', '}', '[', ']', '/', '\\', '<', '>', '\'', '\"');
        foreach ($invalidchars as $char) {
            if (strpos($weblog, $char) > 0 || strpos($weblog, $char) === 0) {
                if ($invalid_input === 'alert') {
                    echo 'Error! Parameter "weblog" of exp:entries_number tag contains illegal character.<br><br>'.PHP_EOL;
                }
                $found_invalid = true;
            }
        }

        foreach ($invalidchars as $char) {
            if (strpos($categoryid, $char) > 0 || strpos($categoryid, $char) === 0) {
                if ($invalid_input === 'alert') {
                    echo 'Error! Parameter "category" of exp:entries_number tag contains illegal character.<br><br>'.PHP_EOL;
                }
                $found_invalid = true;
            }
        }

        foreach ($invalidchars as $char) {
            if (strpos($urltitle, $char) > 0 || strpos($urltitle, $char) === 0) {
                if ($invalid_input === 'alert') {
                    echo 'Error! Parameter "url_title" of exp:entries_number tag contains illegal character.<br><br>'.PHP_EOL;
                }
                $found_invalid = true;
            }
        }

        foreach ($invalidchars as $char) {
            if (strpos($entryid, $char) > 0 || strpos($entryid, $char) === 0) {
                if ($invalid_input === 'alert') {
                    echo 'Error! Parameter "entry_id" of exp:entries_number tag contains illegal character.<br><br>'.PHP_EOL;
                }
                $found_invalid = true;
            }
        }

        foreach ($invalidchars as $char) {
            if (strpos($site, $char) > 0 || strpos($site, $char) === 0) {
                if ($invalid_input === 'alert') {
                    echo 'Error! Parameter "site" of exp:entries_number tag contains illegal character.<br><br>'.PHP_EOL;
                }
                $found_invalid = true;
            }
        }

        if ($found_invalid === false) {
            // If "category" parameter is defined
            if ($categoryid !== false) {
                // Clean whitespace from "category" parameter value
                $categoryid = str_replace(' ', '', $categoryid);
                // Check if "category" param contains "not"
                // // In case "category" param contains "not" form SQL clause using "AND" and "!=" operators
                if (strpos($categoryid, 'not')===0) {
                    $categoryid = substr($categoryid, 3);
                    $categoryidarray = explode('|', $categoryid);
                    foreach ($categoryidarray as $categoryidnumber) {
                        $categoryidclause .= " AND exp_category_posts.cat_id!='".$categoryidnumber."' ";
                    }
                    //exit('$categoryidclause: '.$categoryidclause);
                } else {
                    // the case "category" param does not contain "not"
                    $categoryidarray = explode('|', $categoryid);
                    $categoryidarray2 = explode('&', $categoryid);
                    // the case in "category" param there is neither "|" symbol nor "&" symbol
                    if (count($categoryidarray)==1 && count($categoryidarray2)==1) {
                        $categoryidclause = " AND exp_category_posts.cat_id='".$categoryidarray[0]."' ";
                    } elseif (count($categoryidarray) > 1) {
                        //the case in "category" param there is at least one "|" symbol
                        foreach ($categoryidarray as $categoryidnumber) {
                            $categoryidclause .= " OR exp_category_posts.cat_id='".$categoryidnumber."' ";
                        }
                        $categoryidclause = substr($categoryidclause, 4);
                        $categoryidclause = " AND (".$categoryidclause.")";
                        $distinctoperator = ' DISTINCT ';
                    } elseif (count($categoryidarray2)>1) {
                    //the case in "category" param there is at least one "&" symbol
                        //echo 'count($categoryidarray2): '.count($categoryidarray2).'<br><br>';
                        foreach ($categoryidarray2 as $categoryidnumber2) {
                            $categoryidclause .= " OR exp_category_posts.cat_id='".$categoryidnumber2."' ";
                        }
                        $categoryidclause = substr($categoryidclause, 4);
                        $categoryidclause = " AND (".$categoryidclause.")";
                        $groupbyclause = " GROUP BY exp_channel_titles.entry_id ";
                        $havingclause = " HAVING count(exp_channel_titles.entry_id) = '".count($categoryidarray2)."' ";
                    }
                    //echo '$categoryidclause: '.$categoryidclause.'<br><br>';
                    //echo '$havingclause: '.$havingclause.'<br><br>';
                }
                // Form category related clauses
                $entryidequalityclause = " AND exp_category_posts.entry_id=exp_channel_titles.entry_id ";
                $categorytablesclause = ", exp_category_posts ";
            }
            
            // If "category_group" parameter is defined
            if ($category_group !== false) {
                $category_group_clause = '';
                $category_group = str_replace(' ', '', $category_group);
                if (strpos($category_group, 'not') === 0) {
                    $category_group_clause .= ' NOT ';
                    $category_group = substr($category_group, 3);
                }
                $category_group_clause .= " IN ('".str_replace("|", "', '", $category_group)."') ";
                $category_group_clause = ' AND exp_channel_titles.entry_id IN ( SELECT exp_channel_titles.entry_id FROM exp_channel_titles INNER JOIN exp_category_posts ON exp_category_posts.entry_id=exp_channel_titles.entry_id INNER JOIN exp_categories ON exp_category_posts.cat_id=exp_categories.cat_id WHERE exp_categories.group_id '.$category_group_clause.' ) ';
                //echo '$category_group_clause: ['.$category_group_clause.']';
            }
            
            // If "author_group" parameter is defined
            if ($author_group !== false) {
                $author_group_clause = '';
                $author_group = str_replace(' ', '', $author_group);
                if (strpos($author_group, 'not') === 0) {
                    $author_group_clause .= ' NOT ';
                    $author_group = substr($author_group, 3);
                }
                $author_group_clause .= " IN ('".str_replace("|", "', '", $author_group)."') ";
                $author_group_clause = ' AND exp_channel_titles.entry_id IN ( SELECT exp_channel_titles.entry_id FROM exp_channel_titles INNER JOIN exp_members ON exp_members.member_id=exp_channel_titles.author_id WHERE exp_members.role_id '.$author_group_clause.' ) ';
                //echo '$author_group_clause: ['.$author_group_clause.']';
            }
            
            // If "weblog" parameter is defined
            if ($weblog !== false) {
                // Clean whitespace from "weblog" parameter value
                $weblog = str_replace(' ', '', $weblog);
                // Check if "weblog" param contains "not"
                if (strpos($weblog, 'not')===0) {
                    // In case "weblog" param contains "not" form SQL clause using "AND" and "!=" operators
                    $weblog = substr($weblog, 3);
                    $weblogarray = explode('|', $weblog);
                    foreach ($weblogarray as $weblogname) {
                        $weblogclause .= " AND exp_channels.channel_name!='".$weblogname."' ";
                    }
                    //exit('$weblogclause: '.$weblogclause);
                } else {
                    // In case "weblog" param does not contain "not" form SQL clause using "OR" and "=" operators
                    $weblogarray = explode('|', $weblog);
                    if (count($weblogarray)==1) {
                        $weblogclause = " AND exp_channels.channel_name='".$weblogarray[0]."' ";
                    } else {
                        foreach ($weblogarray as $weblogname) {
                            $weblogclause .= " OR exp_channels.channel_name='".$weblogname."' ";
                        }
                        $weblogclause = substr($weblogclause, 4);
                        $weblogclause = " AND (".$weblogclause.") ";
                    }
                    //exit('$weblogclause: '.$weblogclause);
                }
            }
            
            // If "author_id" parameter is defined
            if ($author_id !== false) {
                $author_id = str_replace('{logged_in_member_id}', ee()->session->userdata['member_id'], $author_id);
                // Clean whitespace from "author_id" parameter value
                $author_id = str_replace(' ', '', $author_id);
                // Check if "author_id" param contains "not"
                if (strpos($author_id, 'not')===0) {
                    // In case "author_id" param contains "not" form SQL clause using "AND" and "!=" operators
                    $author_id = substr($author_id, 3);
                    $authoridarray = explode('|', $author_id);
                    foreach ($authoridarray as $authoridnum) {
                        $authoridclause .= " AND exp_channel_titles.author_id!='".$authoridnum."' ";
                    }
                } else {
                    // In case "author_id" param does not contain "not" form SQL clause using "OR" and "=" operators
                    $authoridarray = explode('|', $author_id);
                    if (count($authoridarray)==1) {
                        $authoridclause = " AND exp_channel_titles.author_id='".$authoridarray[0]."' ";
                    } else {
                        foreach ($authoridarray as $authoridnum) {
                            $authoridclause .= " OR exp_channel_titles.author_id='".$authoridnum."' ";
                            $authoridclause = substr($authoridclause, 4);
                            $authoridclause = " AND (".$authoridclause.") ";
                        }
                    }
                }
                //echo 'authoridclause: '.$authoridclause.'<br><br>';
            }
            
            // Form status clause
            // By default not display entries having status "closed"
            $statusclause = " AND exp_channel_titles.status NOT IN ('closed') ";
            if ($status !== false) {
                // Check if "status" param contains "not"
                if (strpos($status, 'not')===0) {
                    // In case "status" param contains "not" form SQL clause using "AND" and "!=" operators
                    $status = substr($status, 3);
                    $statusarray = explode('|', $status);
                    foreach ($statusarray as $statusname) {
                        $statusname = trim($statusname);
                        $statusclause .= " AND exp_channel_titles.status!='".$statusname."' ";
                    }
                    //echo '$statusclause: '.$statusclause;
                } else {
                    // In case "status" param does not contain "not" form SQL clause using "OR" and "=" operators
                    $statusarray = explode('|', $status);
                    if (count($statusarray)==1) {
                        $statusclause = " AND exp_channel_titles.status='".$statusarray[0]."' ";
                    } else {
                        foreach ($statusarray as $statusname) {
                            $statusname = trim($statusname);
                            $statusclause .= " OR exp_channel_titles.status='".$statusname."' ";
                        }
                        $statusclause = substr($statusclause, 4);
                        $statusclause = " AND (".$statusclause.") ";
                    }
                }
                //echo '$statusclause: '.$statusclause.'<br><br>';
            }
            
            // If "site" parameter is defined
            if ($site !== false) {
                // Clean whitespace from "site" parameter value
                $site = str_replace(' ', '', $site);
                // Check if "site" param contains "not"
                if (strpos($site, 'not')===0) {
                    // In case "site" param contains "not" form SQL clause using "AND" and "!=" operators
                    $site = substr($site, 3);
                    $sitearray = explode('|', $site);
                    foreach ($sitearray as $siteid) {
                        $siteclause .= " AND exp_channel_titles.site_id!='".$siteid."' ";
                        $siteclause2 .= " AND exp_sites.site_id!='".$siteid."' ";
                    }
                    //exit('$siteclause: '.$siteclause);
                } else {
                    // In case "site" param does not contain "not" form SQL clause using "OR" and "=" operators
                    $sitearray = explode('|', $site);
                    if (count($sitearray)==1) {
                        $siteclause = " AND exp_channel_titles.site_id='".$sitearray[0]."' ";
                        $siteclause2 = " AND exp_sites.site_id='".$sitearray[0]."' ";
                    } else {
                        foreach ($sitearray as $siteid) {
                            $siteclause .= " OR exp_channel_titles.site_id='".$siteid."' ";
                            $siteclause2 .= " OR exp_sites.site_id='".$siteid."' ";
                        }
                        $siteclause = substr($siteclause, 4);
                        $siteclause2 = substr($siteclause2, 4);
                        $siteclause = " AND (".$siteclause.") ";
                        $siteclause2 = " AND (".$siteclause2.") ";
                        //exit('$siteclause: '.$siteclause);
                    }
                }
            }
            
            if ($urltitle !== false) {
                $urltitleclause = " AND exp_channel_titles.url_title ";
                $urltitle = trim($urltitle);
                if (strpos($urltitle, 'not') === 0) {
                    $urltitle .= ' NOT ';
                    $urltitle = substr($urltitle, 3);
                    $urltitle = trim($urltitle);
                }
                $urltitleclause .= " IN ('".str_replace("|", "','", $urltitle)."') ";
            }
            
            if ($entryid !== false) {
                $entryidclause = " AND exp_channel_titles.entry_id ";
                $entryid = trim($entryid);
                if (strpos($entryid, 'not') === 0) {
                    $entryid .= ' NOT ';
                    $entryid = substr($entryid, 3);
                    $entryid = trim($entryid);
                }
                $entryidclause .= " IN ('".str_replace("|", "','", $entryid)."') ";
            }
            
            // Form expired entries clause
            if ($show_expired === false) {
                $expiredentriesclause = " AND (exp_channel_titles.expiration_date = '0' OR exp_channel_titles.expiration_date > '".ee()->localize->now."') ";
            }
            
            // Form future entries clause
            if ($show_future_entries === false) {
                $futureentriesclause = " AND exp_channel_titles.entry_date < '".ee()->localize->now."' ";
            }
            
            // Form start on relative clause
            if ($start_on_relative) {
                $start_on_relative_date = ee()->localize->now - $start_on_relative;
                $start_on_relative_clause = " AND exp_channel_titles.entry_date > '".$start_on_relative_date."' ";
            }
            
            // Form stop before relative clause
            if ($stop_before_relative) {
                $stop_before_relative_date = ee()->localize->now - $stop_before_relative;
                $stop_before_relative_clause = " AND exp_channel_titles.entry_date < '".$stop_before_relative_date."' ";
            }
            
            // If "start_on" parameter is defined
            if ($start_on) {
                if (method_exists(ee()->localize, 'convert_human_date_to_gmt') == true) {
                    $start_on = ee()->localize->convert_human_date_to_gmt($start_on);
                } else {
                    $start_on = ee()->localize->string_to_timestamp($start_on);
                }
                $start_on_clause = " AND exp_channel_titles.entry_date > '".$start_on."' ";
            }
            
            // If "stop_before" parameter is defined
            if ($stop_before) {
                if (method_exists(ee()->localize, 'convert_human_date_to_gmt') == true) {
                    $stop_before = ee()->localize->convert_human_date_to_gmt($stop_before);
                } else {
                    $stop_before = ee()->localize->string_to_timestamp($stop_before);
                }
                $stop_before_clause = " AND exp_channel_titles.entry_date < '".$stop_before."' ";
            }

            if ($month) {
                $month = sprintf("%02d", $month);
                $monthClause = " AND exp_channel_titles.month = '{$month}'";
            }
            if ($year) {
                $year = sprintf("%02d", $year);
                $yearClause = " AND exp_channel_titles.year = '{$year}'";
            }
            
            // Form fieldclause
            // form fieldclause
            if ($field_name !== false && $field_value !== false) {
                $field_id = 0;
                $sql_field_id = "SELECT exp_channel_fields.field_id 
                                FROM exp_channel_fields
                                WHERE exp_channel_fields.field_name='".$field_name."' ".$siteclause2." 
                                LIMIT 1";
                $query_field_id = ee()->db->query($sql_field_id);

                if ($query_field_id->num_rows() == 1) {
                    $field_id_row = $query_field_id->row_array();
                    $field_id = $field_id_row['field_id'];

                    $fieldModel = ee('Model')->get('ChannelField', $field_id)->first();

                    $table = $fieldModel->legacy_field_data ? 'exp_channel_data' : 'exp_channel_data_field_' . $field_id;

                    $compare = ' = ';
                    if ($field === "exclude" || $field === "!") {
                        $compare = '!= ';
                    }
                    if ($field === "like") {
                        $compare = ' LIKE ';
                        $field_value = '%'.$field_value.'%';
                    }
                    // Searching for entries having/not having certain field empty
                    if ($field_value == "IS_EMPTY") {
                        $field_value = '';
                    }
                    if ($field_value == "IS_NOT_EMPTY") {
                        $field_value = '';
                        $compare = '!= ';
                    }
                    if ($field_id) {
                        $fieldclause = " AND exp_channel_titles.entry_id IN ( 
                                                                 SELECT exp_channel_titles.entry_id 
                                                                 FROM 
                                                                     exp_channel_titles
                                                                         INNER JOIN
                                                                     {$table}
                                                                         ON
                                                                     {$table}.entry_id = exp_channel_titles.entry_id 
                                                                 WHERE {$table}.field_id_".$field_id.$compare."'".$field_value."'
                                                        ) ";
                    }
                }
            }
            
                // Create SQL query string
            $todo = "SELECT ".$distinctoperator." exp_channel_titles.url_title, exp_channel_titles.title, exp_channel_titles.entry_id, exp_channel_titles.status, exp_channel_titles.expiration_date, exp_channel_titles.author_id, exp_channels.channel_name FROM exp_channel_titles, exp_channels ".$categorytablesclause." WHERE exp_channel_titles.channel_id=exp_channels.channel_id ";
            $todo .= $entryidequalityclause.$categoryidclause.$category_group_clause.$author_group_clause.$weblogclause.$urltitleclause.$entryidclause.$statusclause.$authoridclause.$siteclause.$expiredentriesclause.$futureentriesclause.$start_on_relative_clause.$stop_before_relative_clause.$start_on_clause.$stop_before_clause.$monthClause.$yearClause.$fieldclause.$groupbyclause.$havingclause;
            //echo '$todo: '.$todo.'<br><br>';
            
            // Perform SQL query
            $query = ee()->db->query($todo);
            
            //$query_array = $query->result_array();
            // var_dump($query_array);
            
            //Find number of entries
            $entriesnumber = $query->num_rows();
            
            //Create conditionals array
            $conds['entry_count'] = $entriesnumber;
            
            //Make entry_count variable available for use in conditionals
            $tagdata = ee()->functions->prep_conditionals($tagdata, $conds);
            
            // Output the value of {entry_count} variable
            $tagdata = str_replace('{entry_count}', $entriesnumber, $tagdata);
            
            $this->return_data = $tagdata;
        }
    }
}
