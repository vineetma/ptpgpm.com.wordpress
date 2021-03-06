<?php
/**
 * The template used for displaying tables content in page.php
 * User needs to capture database query strings in the page in following format
 * {query_meta { <<query1>> } <<key_field>> {<<query2>>}{<<query3>>} print {<<print_fields>>}}
 * Parameters explanation (the ones which are in <<xxx>> angular braces
 * @query1 select statement for mysql that joins the primary table and meta data table. \
 *      here author should use only one field and with distinct keyword
 * @key_field name of the field which is being indexed for the final outcome e.g. id in a user database
 * @query2 select statement that will fetch data from primary table for all the user ids \
 *         that have been fetched by query1
 * @query3 select statement that will run on meta data to fetch all meta fields for every user
 * @print_fields this is a string to capture the name of the fields that shall be printed in table \
 *         with their Heading/Titles. These should be comma separated
 *         format is: <<field_name>>=><<Heading>>, <<field_name>>=><<Heading>>
 *    Example: user_id => User Id, user_email => E-Mail          
 *    
 * Example: Trying to selected fields related to user from wordpress database
 * {query_meta {SELECT distinct(user_id) FROM `ptpg_users`join `ptpg_usermeta` where \
 * ID=user_id and ((meta_key="first_name" and meta_value like "$search_text") or \
 * (meta_key="last_name" and meta_value like "$search_text") or (user_login like "$search_text") \
 * or (display_name like "$search_text"));} user_id {select ID, user_email, display_name, \
 * user_login from ptpg_users where ID=$user_id;} \
 * {select meta_key, meta_value from ptpg_usermeta where user_id=$user_id;} print \
 * {first_name=>First Name, last_name=>Last Name, user_contact=>Contact Number, \
 * display_name=>Display Name, user_email=>E-Mail, user_login=>Student ID}}
 *
 * @package WordPress
 * @subpackage Twenty_Twelve (modified / enhanced by Vineet Maheshwari)
 * @since Twenty Twelve 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<header class="entry-header">
  <h1 class="entry-title"><?php the_title(); ?></h1>
</header>

<div class="entry-content">
  <form method="post" id="table_<?php the_ID();?>" action="<?php echo the_permalink();?>">
  <?php 
    /* Read the content, fetch data from table and print it */
    $user_input = get_the_content();
    $result[] = array();
    // search for opening brace in the text
    $x = strpos($user_input, "{");
    $plain_text_content = substr($user_input,0,$x-1);
    echo $plain_text_content;
    // find command (query_meta)
    $y = strpos($user_input, " ", $x);
    $cmd = substr($user_input, $x+1, $y-$x-1);
    //	   echo "Command: $cmd....";
    if($cmd == "query_meta") {
      //	     echo "I am in ..";
      $x = $y+1; // now search from here..
      $x = strpos($user_input, "{", $x)+1;
      $y = strpos($user_input,"}",$x);
      $query = substr($user_input, $x, $y-$x);
  if(strlen($_POST['search_text']) < 4) {?>
  <h2>Enter at least 4 characters in search box</h2>
  <?php 
    } else if (!preg_match("/^[a-zA-Z0-9]*$/", $_POST['search_text'])) {
  ?> <h2>Remove non-alphanumeric characters from search box</h2>
  <?php
  } else {
	$query = str_replace("\$search_text", "%".$_POST['search_text']."%", $query);
	//echo $query."<br/>";
	$myrows = $wpdb->get_results($query);
	//	     print_r($myrows);
	$x = $y+1;
	$y = strpos($user_input, "{", $x);
	$key_field = trim(substr($user_input, $x, $y-$x));
	//TODO: if this keyfield exists in the  prev search output
	$x = $y+1;
	$y = strpos($user_input, "}", $x);
	// next opening brace and pick up the query string
	// pick up next opening brace and hence query string2
	$query2 = substr($user_input, $x, $y-$x);
	//echo "Query2: $query2 <br/>$key_field...";
	// pick up id field
	$x = $y+1;
	$y = strpos($user_input, "{", $x);
	$x = $y+1;
	$y = strpos($user_input, "}", $x);
	$query3 = substr($user_input, $x, $y-$x);
	//echo "Query3: $query3 <br/>$key_field...";
	foreach ($myrows as $row) {
	  // iterate through all results
	  // execute query string2 replace id field in string2 with iterator
	  // create array with meta keys as member names per array entry
	  // and meta values as the value of every cell per row
	  //	     echo $row->$key_field."<br/>";
	  $t_query2 = str_replace("\$$key_field", $row->$key_field, $query2);
	  $myrows2 = $wpdb->get_results($t_query2);
	  $t = array();
	  //	       print_r($myrows2);
	  foreach($myrows2[0] as $m=>$v) {
	    $t[$m] = $v;
	  }
	  
	  $t_query3 = str_replace("\$$key_field", $row->$key_field, $query3);
	  //print_r($row);echo "<br/>";
	  //echo $t_query."<br/>";
	  $myrows3 = $wpdb->get_results($t_query3);
	  foreach($myrows3 as $row3) {
	    $t[$row3->meta_key] = $row3->meta_value;
	  }
	  $result[] = $t;
	}
	//echo "<pre>";
	//print_r($result);
	//echo "</pre>";
	// we may like to print it also directly here..
	//	     $print_fields = array('first_name' => "First Name", 'last_name' => 'Last Name');
	$x = $y + 1;
	$y = strpos($user_input, "{", $x);
	$cmd2 = trim(substr($user_input, $x, $y-$x));
	if($cmd2 == "print") {
	  $x = $y + 1;
	  $y = strpos($user_input, "}", $x);
	  $fields = explode(",", substr($user_input, $x, $y-$x));
	  foreach($fields as $f) {
	    $fk = explode("=>", $f);
	    $print_fields[trim($fk[0])] = trim($fk[1]);
	  }
	}
      }
      //	     print_r($print_fields);
  ?>
  <table>
    <tr>
      <?php
      foreach($print_fields as $f =>$h) {?>
      <td><?php echo $h ?></td>
      <?php
	}
      ?></tr>
      <?php
	if(sizeof($result) >1)
	  unset($result[0]);
      foreach($result as $r) {?>
      <tr> <?php
	foreach($print_fields as $f => $h) {
	  ?><td><?php 
	  if(array_key_exists($f, $r))
	    echo $r[$f];
	  ?> </td> <?php
	}
	?></tr><?php
      }
      ?></table><?php
      //	    	     print_r($result); echo "<br/>";
      }
      ?>
      <input type="text" value="" name="search_text" />
      <input type="submit" value="Search" name="search_member" />
    </form>
    <?php //wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
  </div><!-- .entry-content -->
  <footer class="entry-meta">
    <?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
  </footer><!-- .entry-meta -->
</article><!-- #post -->
