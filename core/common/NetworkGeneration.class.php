<?php

/**
 * Class NetworkGeneration
 * 
 * Contains functions for accessing and manipulating alter and bucket data in the database.
 *
 */

class NetworkGeneration {

public function ProcessAPINetwork() {
		$edge_list = SessionManager::GetData('edge_list');
	
		foreach ($edge_list as $edge) {
			
			$uid1 = $edge[uid1];
			$uid2 = $edge[uid2];

			$alter_id1 = DB::get_var("SELECT id from alters WHERE api_uid = $uid1 AND user_id = $uid");
			$alter_id2 = DB::get_var("SELECT id from alters WHERE api_uid = $uid2 AND user_id = $uid");
		
			$network = DB::get_var("SELECT network FROM alters WHERE id = $alter_id1");
			$network .= "$alter_id2,";
		
			$query = "UPDATE alters SET network = '$network' WHERE id = $alter_id1";
			echo $query;
			DB::query($query);
		}
	}

}
?>
