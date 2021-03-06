<?php
function go_clipboard() {
global $wpdb;
$dir = plugin_dir_url(__FILE__);
 add_submenu_page( 'game-on-options.php', 'Clipboard', 'Clipboard', 'manage_options', 'go_clipboard', 'go_clipboard_menu');
}

function go_clipboard_menu() {
		global $wpdb;
	if (!current_user_can('manage_options'))  { 
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	else{
		go_style_clipboard();
		go_jquery_clipboard();
		
		?>
 <div id="records_tabs">
<ul>
    <li><a href="#clipboard_wrap">Clipboard</a></li>
    <li><a href="#go_analysis">Analysis</a></li>
  </ul>
        <div id="clipboard_wrap">
        <select class="menuitem" id="go_clipboard_class_a_choice" onchange="go_clipboard_class_a_choice();">
      <option>...</option>
      
         <?php
$class_a = get_option('go_class_a');
if($class_a){
	foreach($class_a as $key=> $value){
		echo '<option class="ui-corner-all">'.$value.'</option>';
		}
	}
	?></select>
    
    <div id="go_clipboard_add"> <div style="width:17px; display:inline-table;margin-top: 4px;
margin-right: 5px;" title="Check the boxes of the students you want to add to." class="ui-state-default ui-corner-all"><a href="javascript:;" onclick="go_display_help_video('http://maclab.guhsd.net/go/video/clipboard/clipboard.mp4')"> 
<span  class="ui-icon ui-icon-help"></span></a>
</div>
<label for="go_clipboard_points"><?php echo go_return_options('go_points_name'); ?>: </label><input name="go_clipboard_points" id="go_clipboard_points" /> 
<label id="go_clipboard_minutes"><?php echo 'Minutes'; ?>: </label> <input name="go_clipboard_minutes" id="go_clipboard_time" />
<label for="go_clipboard_currency"><?php echo go_return_options('go_currency_name'); ?>: </label><input name="go_clipboard_minuts" id="go_clipboard_currency" />
<label for="go_clipboard_infractions">Infractions: </label><input name="go_clipboard_infractions" id="go_clipboard_infractions" />
<label name="go_clipboard_reason"><br /><div style="width:17px; display:inline-table;margin-top: 4px;
margin-right: 5px;" title="The message will be displayed as reason if any points/currency/minutes are being added. If nothing is being added, the message will be displayed as a message in a seperate system in the admin bar with a limit of 9 messages per student."> Message: </label> <textarea name="go_clipboard_reason" id="go_clipboard_reason"></textarea><button class="ui-button-text" id="go_send_message" onclick="go_clipboard_add();">Add</button><button id="go_fix_messages" onclick="fixmessages()">Fix Messages</button></div>

    
    <table  id="go_clipboard_table" class="pretty" >
    <thead>
    <tr><th><input type="checkbox" onClick="toggle(this);" /></th>
     <th class="header" style="width:6%;"><a href="#" >ID</a></th>
 <th class="header" style="width:6%;"><a href="#" ><?php echo go_return_options('go_class_b_name'); ?></a></th>
 <th class="header" style="width:10%;"><a href="#" >Name</a></th>
<th class="header" style="width:10%;""><a href="#" >Gamertag</a></th>
<th class="header" style="width:8%;"><a href="#" >Rank</a></th>
<th class="header" style="width:8%;"><a href="#" ><?php echo go_return_options('go_focus_name'); ?></a></th>
<th class="header" style="width:6%;"><a href="#" ><?php echo go_return_options('go_currency_name'); ?></a></th>
<th class="header" style="width:8%;"><a href="#">Minutes</a></th>
<th class="header" style="width:5%;" align="center"><a href="#"><?php echo go_return_options('go_points_name'); ?></a></th>
<th class="header" style="width:13%;"><a href="#" ><?php echo go_return_options('go_infractions_name'); ?> (Max: <?php echo $current_max_infractions; ?>)</a></th>
<th class="header" style="width:9%;"><a href="#"><?php echo go_return_options('go_first_stage_name'); ?></a></th> 
<th class="header" style="width:8%;"><a href="#" ><?php echo go_return_options('go_second_stage_name'); ?></a></th> 
<th class="header" style="width:8%;"><a href="#" ><?php echo go_return_options('go_third_stage_name'); ?></a></th>  
<th class="header" style="width:14%;"><a href="#"><?php echo go_return_options('go_fourth_stage_name'); ?></a></th> </tr></thead>
<tbody id="go_clipboard_table_body"></tbody>
    
    
    </table>
    </div>
    
     </div>
	 <div id="go_analysis">
     <button onClick="collectData();">Collect Data</button>
     <select id="go_selection" onClick="go_update_graph();">
     <option value="0"><?php echo 'Minutes'; ?></option>
     <option value="1"><?php echo go_return_options('go_points_name'); ?></option>
     <option value="2"><?php echo go_return_options('go_third_stage_name'); ?></option>
     <option value="3"><?php echo go_return_options('go_fourth_stage_name'); ?></option>
     </select>
     <p id="choices"> 
     </p>
     <div class="container">
     <div id="placeholder" style="width:98%;height:98%;">
     </div>  
     </div>
     </div>
     </div>
	 <?php
	}
}



function go_clipboard_intable(){
	global $wpdb;
	$class_a_choice = $_POST['go_clipboard_class_a_choice'];
	$table_name_user_meta = $wpdb->prefix.'usermeta';
	$table_name_go = $wpdb->prefix.'go';
	$uid = $wpdb->get_results("SELECT user_id
FROM ".$table_name_user_meta."
WHERE meta_key =  'wp_capabilities'
AND meta_value LIKE  '%subscriber%'");
	$focuses = (array)get_option('go_focus');
	$focuses_list = '';
	foreach($focuses as $focus){
		$focuses_list .= '<option name="'.$focus.'" value="'.$focus.'">'.$focus.'</option>';
	}
	foreach($uid as $id){
		foreach($id as $value){
			$class_a = get_user_meta($value, 'go_classifications',true);
			if($class_a){ 
			if($class_a[$class_a_choice]){
		$user_data_key = get_userdata( $value ); 
		$user_login = $user_data_key->user_login;
		$user_display = $user_data_key->display_name;
		$user_first_name = $user_data_key->user_firstname;
		$user_last_name =  $user_data_key->user_lastname;
		$user_url =  $user_data_key->user_url;
		$user_focuses = go_display_user_focuses($value);
		$infractions = go_return_infractions($value);
		$minutes = go_return_minutes($value);
		$currency = go_return_currency($value);
		$points = go_return_points($value);
		go_get_rank($value);
		global $current_rank;
		$first_stage = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $value and status = 1");
		$second_stage = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $value and status = 2");
		$third_stage = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $value and status = 3");
		$fourth_stage = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $value and status = 4");
		
		echo '<tr><td><input class="go_checkbox" type="checkbox" name="go_selected" value="'.$value.'"></td><td><span><a href="#" onclick="go_admin_bar_stats_page_button('.$value.'); "  >'.$user_login.'</a></td><td>'.$class_a[$class_a_choice].'</td><td><a href="'.$user_url.'" target="_blank">'.$user_last_name.', '.$user_first_name.'</a></td><td>'.$user_display.'</td><td>'.$current_rank.'</td><td><select id="go_focus" onchange="go_user_focus_change('.$value.', this);"><option name="'.$user_focuses.'" value="'.$user_focuses.'">'.$user_focuses.' </option>'.$focuses_list.'</select></td><td>'.$currency.'</td><td>'.$minutes.'</td><td>'.$points.'</td><td>'.$infractions.'</td><td>'.$first_stage.'</td><td>'.$second_stage.'</td><td>'.$third_stage.'</td><td>'.$fourth_stage.'</td></tr>';
		
		}}}}
		die();
	}

add_action('wp_ajax_go_clipboard_add','go_clipboard_add');
function go_clipboard_add(){
	$ids = $_POST['ids'];
	$points = $_POST['points'];
	$currency = $_POST['currency'];
	$minutes = $_POST['time'];
	$reason = $_POST['reason'];
	$infractions = $_POST['infractions'];
	foreach($ids as $key=>$value){
	if($points != ''&& $reason != ''){
	go_add_currency($value,$reason, 6, $points, 0, false);
	}
if($currency!= ''&&$reason!= ''){
	go_add_currency($value, $reason, 6, 0, $currency, false);

	}
if($minutes!= ''&&$reason != ''){
	go_add_minutes($value, $minutes, $reason);
	}
	if($infractions!=''&&$reason !=''){
	go_add_infraction($value, $infractions,true);
}
if($infractions==''&& $points == '' && $currency== ''&& $minutes== ''&& $reason !=''){
	$user_id = $value;
	$current_messages = get_user_meta($user_id, 'go_admin_messages',true);
	$current_messages[1][time()] = array($reason, 1);
	krsort($current_messages[1]);
	if(count($current_messages[1]) > 9){
		array_pop($current_messages[1]);
		}
	if(!$current_messages[0]){
		$current_messages[0] = 1;
		} else {
			(int)$current_messages[0] = (int)$current_messages[0] + 1;
			if((int)$current_messages[0] > 9){(int)$current_messages[0] = 9;}
			}
update_user_meta( $user_id, 'go_admin_messages', $current_messages);
}
	}
	die();
	
	}
function go_clipboard_collect_data(){
	global $wpdb;
	$table_name_user_meta = $wpdb->prefix.'usermeta';
	$table_name_go = $wpdb->prefix.'go';
	$uid = $wpdb->get_results("SELECT user_id
FROM ".$table_name_user_meta."
WHERE meta_key =  'wp_capabilities'
AND meta_value LIKE  '%subscriber%'");
	$time = round(microtime(true));
	$array = get_option('go_graphing_data');
	foreach($uid as $id){
		foreach($id as $value){
		$minutes = go_return_minutes($value);
		$points = go_return_points($value);
		$third_stage = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $value and status = 3");
		$fourth_stage = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $value and status = 4");
		$array[$value][$time] = $minutes.','. $points.','. $third_stage.','. $fourth_stage;
		}}
	update_option( 'go_graphing_data', $array );
	}
	
function go_clipboard_get_data(){
	global $wpdb;
	$selection = $_POST['go_graph_selection'];
	$array = get_option('go_graphing_data',false);
	foreach($array as $id => $date){
		$getinfo = get_userdata( $id );
		$id= $getinfo -> user_login;
		$first= $getinfo-> first_name;
		$last= $getinfo-> last_name;
		$info[$id]['label'] = $last.', '.$first.' ('.$id.')';
		foreach($date as $date => $content){
			$content_array = explode(',',$content);
			$info[$id]['data'][]=array($date*1000,$content_array[$selection]);
			//$data[$id] .= '['.$date.','.$content_array[$selection].'],';
			}
		//$info .= '"'.$id.'": {label: "'.$id.'",data: ['.$data[$id].']},';
		}
		

		echo JSON_encode($info);
		//	echo '{'.$info.'}';
			die();
			     	}

add_action('wp_ajax_fixmessages', 'fixmessages');					
function fixmessages(){
	global $wpdb;
	$users = get_users(array('role' => 'Subscriber'));
	foreach($users as $user){
		$messages = get_user_meta($user->ID, 'go_admin_messages',true);
		$messages_array = $messages[1];
		$messages_unread = array_values($messages_array);
		$messages_unread_count = 0;
		foreach($messages_unread as $message_unread){
			if($message_unread[1] == 1){
				$messages_unread_count++;	
			}
		}
		if($messages[0] != $message_unread_count){
			$messages[0] = $messages_unread_count;
			update_user_meta($user->ID, 'go_admin_messages', $messages);
		}
	}
	
	die();
}
?>