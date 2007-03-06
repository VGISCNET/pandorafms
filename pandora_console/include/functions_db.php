<?php

// Pandora - The Free Monitoring System
// This code is protected by GPL license.
// Este codigo esta protegido por la licencia GPL.
// Sancho Lerena <slerena@gmail.com>, 2003-2006
// Raul Mateos <raulofpandora@gmail.com>, 2005-2006

// Database functions

// --------------------------------------------------------------- 
// give_acl ()
// Main Function to get access to resources
// Return 0 if no access, > 0  if access
// --------------------------------------------------------------- 

function give_acl($id_user, $id_group, $access){	
	// IF user is level = 1 then always return 1
	// Access can be:
	/*	
		IR - Incident Read
		IW - Incident Write
		IM - Incident Management
		AR - Agent Read
		AW - Agent Write
		LW - Alert Write
		UM - User Management
		DM - DB Management
		LM - Alert Management
		PM - Pandora Management
	*/
	
	// Conexion con la base Datos 
	require("config.php");
	$query1="SELECT * FROM tusuario WHERE id_usuario = '".$id_user."'";
	$res=mysql_query($query1);
	$row=mysql_fetch_array($res);
	if ($row["nivel"] == 1)
		$result = 1;
	else {
		if ($id_group == 0) // Group doesnt matter, any group, for check permission to do at least an action in a group
			$query1="SELECT * FROM tusuario_perfil WHERE id_usuario = '".$id_user."'";	// GroupID = 0, group doesnt matter (use with caution!)
		else
			$query1="SELECT * FROM tusuario_perfil WHERE id_usuario = '".$id_user."' and ( id_grupo =".$id_group." OR id_grupo = 1)";	// GroupID = 1 ALL groups      
		$resq1=mysql_query($query1);  
		$result = 0; 
		while ($rowdup=mysql_fetch_array($resq1)){
			$id_perfil=$rowdup["id_perfil"];
			// For each profile for this pair of group and user do...
			$query2="SELECT * FROM tperfil WHERE id_perfil = ".$id_perfil;    
			$resq2=mysql_query($query2);  
			if ($rowq2=mysql_fetch_array($resq2)){
				switch ($access) {
					case "IR": $result = $result + $rowq2["incident_view"]; break;
					case "IW": $result = $result + $rowq2["incident_edit"]; break;
					case "IM": $result = $result + $rowq2["incident_management"]; break;
					case "AR": $result = $result + $rowq2["agent_view"]; break;
					case "AW": $result = $result + $rowq2["agent_edit"]; break;
					case "LW": $result = $result + $rowq2["alert_edit"]; break;
					case "LM": $result = $result + $rowq2["alert_management"]; break;
					case "PM": $result = $result + $rowq2["pandora_management"]; break;
					case "DM": $result = $result + $rowq2["db_management"]; break;
					case "UM": $result = $result + $rowq2["user_management"]; break;
				}
			} 
		}
	} // else
	if ($result > 1)
		$result = 1;
        return $result; 
} 

// --------------------------------------------------------------- 
// audit_db, update audit log
// --------------------------------------------------------------- 

function audit_db($id,$ip,$accion,$descripcion){
	require("config.php");
	$today=date('Y-m-d H:i:s');
	$sql1='INSERT INTO tsesion (ID_usuario, accion, fecha, IP_origen,descripcion) VALUES ("'.$id.'","'.$accion.'","'.$today.'","'.$ip.'","'.$descripcion.'")';
	$result=mysql_query($sql1);
}


// --------------------------------------------------------------- 
// logon_db, update entry in logon audit
// --------------------------------------------------------------- 

function logon_db($id,$ip){
	require("config.php");
	audit_db($id,$ip,"Logon","Logged in");
	// Update last registry of user to get last logon
	$sql2='UPDATE tusuario fecha_registro = $today WHERE id_usuario = "$id"';
	$result=mysql_query($sql2);
}

// --------------------------------------------------------------- 
// logoff_db, also adds audit log
// --------------------------------------------------------------- 

function logoff_db($id,$ip){
	require("config.php");
	audit_db($id,$ip,"Logoff","Logged out");
}

// --------------------------------------------------------------- 
// Returns profile given ID
// --------------------------------------------------------------- 

function dame_perfil($id){ 
	require("config.php");
	$query1="SELECT * FROM tperfil WHERE id_perfil =".$id;
	$resq1=mysql_query($query1);  
	if ($rowdup=mysql_fetch_array($resq1)){
		$cat=$rowdup["name"]; 
	}
		else $cat = "";
	return $cat; 
}


// --------------------------------------------------------------- 
// Returns group given ID
// --------------------------------------------------------------- 

function dame_grupo($id){ 
	require("config.php");
	$query1="SELECT * FROM tgrupo WHERE id_grupo =".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$cat=$rowdup["nombre"];
	}
		else $cat = "";
	return $cat; 
}

// --------------------------------------------------------------- 
// Returns icon name given group ID
// --------------------------------------------------------------- 

function dame_grupo_icono($id){
	require("config.php");
	$query1="SELECT * FROM tgrupo WHERE id_grupo =".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$cat=$rowdup["icon"];
	}
		else $cat = "";
	return $cat;
}

// --------------------------------------------------------------- 
// Return agent id given name of agent
// --------------------------------------------------------------- 

function dame_agente_id($nombre){
	require("config.php");
	$query1="SELECT * FROM tagente WHERE nombre = '".$nombre."'";
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["id_agente"];
	else
		$pro = "";
	return $pro;
}


// --------------------------------------------------------------- 
// Returns userid given name an note id
// --------------------------------------------------------------- 

function give_note_author ($id_note){ 
	require("config.php");
	$query1="SELECT * FROM tnota WHERE id_nota = ".$id_note;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["id_usuario"];
	else
		$pro = "";
	return $pro;
}


// --------------------------------------------------------------- 
// Returns agent id given name of agent
// --------------------------------------------------------------- 

function dame_agente_modulo_id($id_agente, $id_tipomodulo, $nombre){
	require("config.php");
	$query1="SELECT * FROM tagente_modulo WHERE id_agente = ".$id_agente." and id_tipo_modulo = ".$id_tipomodulo." and nombre = '".$nombre."'";
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["id_agente_modulo"];
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Returns event description given it's id
// --------------------------------------------------------------- 

function return_event_description ($id_event){
	require("config.php");
	$query1="SELECT evento FROM tevento WHERE id_evento = $id_event";
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup[0];
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Return ID_Group from an event given as id_event
// --------------------------------------------------------------- 

function gime_idgroup_from_idevent($id_event){
	require("config.php");
	$query1="SELECT * FROM tevento WHERE id_evento = ".$id_event;
	$pro = -1;
	if ($resq1=mysql_query($query1))
		if ($rowdup=mysql_fetch_array($resq1))
			$pro=$rowdup["id_grupo"]; 
	return $pro;
}


// --------------------------------------------------------------- 
// Return module id given name of module type
// --------------------------------------------------------------- 

function dame_module_id($nombre){
	require("config.php"); 
	$query1="SELECT * FROM ttipo_modulo WHERE nombre = '".$nombre."'"; 
	$resq1=mysql_query($query1);  
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["id_tipo"]; 
	else
		$pro = "";
	return $pro; 
}


// --------------------------------------------------------------- 
// Returns agent name when given its ID
// --------------------------------------------------------------- 

function dame_nombre_agente ($id){
	require("config.php"); 
	$query1="SELECT * FROM tagente WHERE id_agente = ".$id; 
	$resq1=mysql_query($query1);  
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["nombre"]; 
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Returns password (HASH) given user_id
// --------------------------------------------------------------- 

function dame_password($id_usuario){
	require("config.php"); 
	$query1="SELECT * FROM tusuario WHERE id_usuario= '".$id_usuario."'"; 
	$resq1=mysql_query($query1);  
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["password"]; 
	else
		$pro = "";
	return $pro; 
}


// --------------------------------------------------------------- 
// Returns name of an alert given ID
// --------------------------------------------------------------- 

function dame_nombre_alerta($id){
	require("config.php");
	$query1="SELECT * FROM talerta WHERE id_alerta = ".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["nombre"]; 
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Returns name of a modules group
// --------------------------------------------------------------- 

function dame_nombre_grupomodulo($id){
	require("config.php");
	$query1="SELECT * FROM tmodule_group WHERE id_mg = ".$id; 
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["name"]; 
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Returns agent name, given a ID of agente_module table
// --------------------------------------------------------------- 

function dame_nombre_agente_agentemodulo($id_agente_modulo){
	require("config.php");
	$query1="SELECT * FROM tagente_modulo WHERE id_agente_modulo = ".$id_agente_modulo;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro = dame_nombre_agente($rowdup["id_agente"]);
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Return agent module, given a ID of agente_module table
// --------------------------------------------------------------- 

function dame_nombre_modulo_agentemodulo($id_agente_modulo){
	require("config.php"); 
	$query1="SELECT * FROM tagente_modulo WHERE id_agente_modulo = ".$id_agente_modulo; 
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro = $rowdup["nombre"];
	else
		$pro = "";
	return $pro;
}


// --------------------------------------------------------------- 
// Return agent module, given a ID of agente_module table
// --------------------------------------------------------------- 

function dame_id_tipo_modulo_agentemodulo($id_agente_modulo){
	require("config.php"); 
	$query1="SELECT * FROM tagente_modulo WHERE id_agente_modulo = ".$id_agente_modulo; 
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro = $rowdup["id_tipo_modulo"];
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Returns name of the user when given ID
// --------------------------------------------------------------- 

function dame_nombre_real($id){
	require("config.php");
	$query1="SELECT * FROM tusuario WHERE id_usuario = '".$id."'";
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["nombre_real"];
	else
		$pro = "";
	return $pro;
}


// --------------------------------------------------------------- 
// This function returns ID of user who has created incident
// --------------------------------------------------------------- 

function give_incident_author($id){
	require("include/config.php");
	$query1="SELECT * FROM tincidencia WHERE id_incidencia = '".$id."'";
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["id_usuario"];
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// This function returns name of server
// --------------------------------------------------------------- 

function give_server_name($id_server){
	require("include/config.php");
	$query1="SELECT * FROM tserver WHERE id_server  = '".$id_server."'";
	$resq1=mysql_query($query1);  
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["name"];
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Return name of a module type when given ID
// --------------------------------------------------------------- 

function dame_nombre_tipo_modulo($id){
	require("config.php");
	$query1="SELECT * FROM ttipo_modulo WHERE id_tipo =".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$pro=$rowdup["nombre"];
	}
	else $pro = "";
	return $pro;
} 

// --------------------------------------------------------------- 
// Return name of a group when given ID
// --------------------------------------------------------------- 

function dame_nombre_grupo($id){
	require("config.php");
	$query1="SELECT * FROM tgrupo WHERE id_grupo =".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$pro=$rowdup["nombre"];
	}
	else $pro = "";
	return $pro;
} 

// --------------------------------------------------------------- 
// This function return group_id given an agent_id
// --------------------------------------------------------------- 

function dame_id_grupo($id_agente){
	require("config.php");
	$query1="SELECT * FROM tagente WHERE id_agente =".$id_agente;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$pro=$rowdup["id_grupo"];
	}
	else $pro = "";
	return $pro;
} 


// --------------------------------------------------------------- 
// Returns number of notes from a given incident
// --------------------------------------------------------------- 

function dame_numero_notas($id){
	require("config.php"); 
	$query1="select COUNT(*) from tnota_inc WHERE id_incidencia =".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$pro=$rowdup["COUNT(*)"]; 
	}
	else $pro = "0";
	return $pro;
}


// --------------------------------------------------------------- 
// Returns number of registries from table of data agents
// --------------------------------------------------------------- 

function dame_numero_datos(){
	require("config.php");
	$query1="select COUNT(*) from tagente_datos";
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$pro=$rowdup["COUNT(*)"];
	}
	else $pro = "0";
	return $pro; 
}


// --------------------------------------------------------------- 
// Returns string packet type given ID
// --------------------------------------------------------------- 

function dame_generic_string_data($id){ 
	// Conexion con la base Datos 
	require("config.php");
	$query1="SELECT * FROM tagente_datos_string WHERE id_tagente_datos_string = ".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$pro=$rowdup["datos"];
	}
	return $pro;
}

// --------------------------------------------------------------- 
// Delete incident given its id and all its notes
// --------------------------------------------------------------- 


function borrar_incidencia($id_inc){
	require("config.php");
	$sql1="DELETE FROM tincidencia WHERE id_incidencia = ".$id_inc;
	$result=mysql_query($sql1);
	$sql3="SELECT * FROM tnota_inc WHERE id_incidencia = ".$id_inc;
	$res2=mysql_query($sql3);
	while ($row2=mysql_fetch_array($res2)){
		// Delete all note ID related in table
		$sql4 = "DELETE FROM tnota WHERE id_nota = ".$row2["id_nota"];
		$result4 = mysql_query($sql4);
	}
	$sql6="DELETE FROM tnota_inc WHERE id_incidencia = ".$id_inc;
	$result6=mysql_query($sql6);
	// Delete attachments
	$sql1="SELECT * FROM tattachment WHERE id_incidencia = ".$id_inc;
	$result=mysql_query($sql1);
	while ($row=mysql_fetch_array($result)){
		// Unlink all attached files for this incident
		$file_id = $row["id_attachment"];
		$filename = $row["filename"];
		unlink ($attachment_store."attachment/pand".$file_id."_".$filename);
	}
	$sql1="DELETE FROM tattachment WHERE id_incidencia = ".$id_inc;
	$result=mysql_query($sql1);
}

// --------------------------------------------------------------- 
// Return SO name given its ID
// --------------------------------------------------------------- 

function dame_so_name($id){
	require("config.php");
	$query1="SELECT * FROM tconfig_os WHERE id_os = ".$id;
	$resq1=mysql_query($query1);  
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["name"];
	else
		$pro = "";
	return $pro;
}
// --------------------------------------------------------------- 
//  Update "contact" field in User table for username $nick
// --------------------------------------------------------------- 

function update_user_contact($nick){	// Sophus simply insist too much in this function... ;)
	require("config.php");
	$today=date("Y-m-d H:i:s",time());
	$query1="UPDATE tusuario set fecha_registro ='".$today."' WHERE id_usuario = '".$nick."'";
	$resq1=mysql_query($query1);
}

// --------------------------------------------------------------- 
// Return SO iconname given its ID
// --------------------------------------------------------------- 

function dame_so_icon($id){ 
	require("config.php");
	$query1="SELECT * FROM tconfig_os WHERE id_os = ".$id;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1))
		$pro=$rowdup["icon_name"];
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Return email of a user given ID 
// --------------------------------------------------------------- 

function dame_email($id){ 
	require("config.php");
	$query1="SELECT * FROM tusuario WHERE id_usuario =".$id;
	$resq1=mysql_query($query1);
	$rowdup=mysql_fetch_array($resq1);
	$nombre=$rowdup["direccion"];
	return $nombre;
} 


// ---------------------------------------------------------------
// Returns Admin value (0 no admin, 1 admin)
// ---------------------------------------------------------------

function dame_admin($id){
	require("config.php");
	$query1="SELECT * FROM tusuario WHERE id_usuario ='".$id."'";   
	$rowdup=mysql_query($query1);
	$rowdup2=mysql_fetch_array($rowdup);
	$admin=$rowdup2["nivel"];
	return $admin;
}

// --------------------------------------------------------------- 
// Gives error message and stops execution if user 
//doesn't have an open session and this session is from an valid user
// --------------------------------------------------------------- 

function comprueba_login() { 
	if (isset($_SESSION["id_usuario"])){
		$id = $_SESSION["id_usuario"];
		require("config.php");
		$query1="SELECT * FROM tusuario WHERE id_usuario = '".$id."'";
		$resq1=mysql_query($query1);
		$rowdup=mysql_fetch_array($resq1);
		$nombre=$rowdup["id_usuario"];
		if ( $id == $nombre ){
			return 0 ;	
		}
	}
	require("general/noaccess.php");
	return 1;	
}

// --------------------------------------------------------------- 
// Gives error message and stops execution if user 
//doesn't have an open session and this session is from an administrator
// --------------------------------------------------------------- 

function comprueba_admin() {
	if (isset($_SESSION["id_usuario"])){
		$iduser=$_SESSION['id_usuario'];
		if (dame_admin($iduser)==1){
			$id = $_SESSION["id_usuario"];
			require("config.php");
			$query1="SELECT * FROM tusuario WHERE id_usuario = '".$id."'";
			$resq1=mysql_query($query1);
			$rowdup=mysql_fetch_array($resq1);
			$nombre=$rowdup["id_usuario"];
			$nivel=$rowdup["nivel"];
			if (( $id == $nombre) and ($nivel ==1))
				return 0;
		}
	}
	require("../general/no_access.php");
	return 1;
}


// ---------------------------------------------------------------
// Returns number of alerts fired by this agent
// ---------------------------------------------------------------

function check_alert_fired($id_agente){
	require("config.php");
	$query1="SELECT * FROM tagente_modulo WHERE id_agente ='".$id_agente."'";   
	$rowdup=mysql_query($query1);
	while ($data=mysql_fetch_array($rowdup)){
		$query2="SELECT COUNT(*) FROM talerta_agente_modulo WHERE times_fired > 0 AND id_agente_modulo =".$data["id_agente_modulo"];
		$rowdup2=mysql_query($query2);
		$data2=mysql_fetch_array($rowdup2);
		if ($data2[0] > 0)
			return 1;
	}
	return 0;
}

// ---------------------------------------------------------------
// 0 if it doesn't exist, 1 if it does, when given email
// ---------------------------------------------------------------

function existe($id){
	require("config.php");
	$query1="SELECT * FROM tusuario WHERE id_usuario = '".$id."'";   
	$resq1=mysql_query($query1);
	if ($resq1 != 0) {
		if ($rowdup=mysql_fetch_array($resq1)){ 
			return 1; 
		}
		else {
			return 0; 
		}
	} else { return 0 ; }
}

// --------------------------------------------------------------- 
// event_insert - Insert event in eventable, using Id_grupo, Id_agente and Evento
// --------------------------------------------------------------- 

function event_insert($evento, $id_grupo, $id_agente){
	require("config.php");
	$today=date('Y-m-d H:i:s');
	$sql1='INSERT INTO tevento (id_agente,id_grupo,evento,timestamp,estado) VALUES ('.$id_agente.','.$id_grupo.',"'.$evento.'","'.$today.'",0)';
	$result=mysql_query($sql1);
}

// --------------------------------------------------------------- 
// Return module interval or agent interval if first not defined
// ---------------------------------------------------------------

function give_moduleinterval($id_agentmodule){ 
	require("config.php");
	$query1="SELECT * FROM tagente_modulo WHERE id_agente_modulo = ".$id_agentmodule;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		if ($rowdup["module_interval"] == 0){ // no module interval defined
			$query2="SELECT * FROM tagente WHERE id_agente = ".$rowdup["id_agente"];
			$resq2=mysql_query($query2);
			if ($rowdup2=mysql_fetch_array($resq2)){
				$interval=$rowdup2["intervalo"];
			}
		} else {
			$interval=$rowdup["module_interval"];
		}
	}
	return $interval;
}

// --------------------------------------------------------------- 
// Return agent interval 
// ---------------------------------------------------------------

function give_agentinterval($id_agent){ 
	require("config.php");
	$query1="SELECT * FROM tagente WHERE id_agente = ".$id_agent;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$interval=$rowdup["intervalo"];
	}
	return $interval;
}

// --------------------------------------------------------------- 
// Return agent_module flag (for network push modules)
// ---------------------------------------------------------------

function give_agentmodule_flag($id_agent_module){ 
	require("config.php");
	$query1="SELECT * FROM tagente_modulo WHERE id_agente_modulo = ".$id_agent_module;
	$resq1=mysql_query($query1);
	if ($rowdup=mysql_fetch_array($resq1)){
		$interval=$rowdup["flag"];
	}
	return $interval;
}

// ---------------------------------------------------------------------- 
// Returns a combo with the groups and defines an array 
// to put all groups with Agent Read permission
// ----------------------------------------------------------------------
function list_group ($id_user){
	$mis_grupos=array (); // Define array mis_grupos to put here all groups with Agent Read permission
	$sql='SELECT id_grupo FROM tgrupo';
	$result=mysql_query($sql);
	while ($row=mysql_fetch_array($result)){
		if ($row["id_grupo"] != 1){
			if (give_acl($id_user,$row["id_grupo"], "AR") == 1){
				array_push ($mis_grupos, $row["id_grupo"]); //Put in  an array all the groups the user belongs
				echo "<option value='".$row["id_grupo"]."'>".
				dame_nombre_grupo($row["id_grupo"])."</option>";
			}
		}
	}
	return ($mis_grupos);
}

// ---------------------------------------------------------------------- 
// Defines an array 
// to put all groups with Agent Read permission
// ----------------------------------------------------------------------

function list_group2 ($id_user){
	$mis_grupos[]=""; // Define array mis_grupos to put here all groups with Agent Read permission
	$sql='SELECT id_grupo FROM tgrupo';
	$result=mysql_query($sql);
	while ($row=mysql_fetch_array($result)){
		if ($row["id_grupo"] != 1){
			if (give_acl($id_user,$row["id_grupo"], "AR") == 1){
				$mis_grupos[]=$row["id_grupo"]; //Put in  an array all the groups the user belongs
			}
		}
	}
	return ($mis_grupos);
}

// --------------------------------------------------------------- 
// Return Group iconname given its name
// --------------------------------------------------------------- 

function show_icon_group($id_group){ 
	$sql="SELECT icon FROM tgrupo WHERE id_grupo='$id_group'";
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result))
		$pro=$row["icon"];
	else
		$pro = "";
	return $pro;
}

// --------------------------------------------------------------- 
// Return Type iconname given its name
// --------------------------------------------------------------- 

function show_icon_type($id_tipo){ 
	$sql="SELECT id_tipo, icon FROM ttipo_modulo WHERE id_tipo='$id_tipo'";
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result))
		$pro=$row["icon"];
	else
		$pro = "";
	return $pro;
}

// ---------------------------------------------------------------
// Return all childs groups of a given id_group inside array $child
// ---------------------------------------------------------------

function give_groupchild($id_group, &$child){
        // Conexion con la base Datos 
        $query1="select * from tgrupo where parent = ".$id_group;
        $resq1=mysql_query($query1);  
        while ($resq1 != NULL && $rowdup=mysql_fetch_array($resq1)){
        	$child[]=$rowdup["id_grupo"];
        }
}

// ---------------------------------------------------------------
// Return true (1) if agent belongs to given group or one of this childs
// ---------------------------------------------------------------

function agent_belong_group($id_agent, $id_group){ 
        // Conexion con la base Datos 
	$child[] = "";
	$child[] = $id_group;
	give_groupchild($id_group,$child);
	$id_agent_group = give_group_id($id_agent);
	if (array_in($child,$id_agent_group)==1){
		return 1; 
	} else {
		return 0;
	}
}

// ---------------------------------------------------------------
// Return true (1) if given group (a) belongs to given groupset
// ---------------------------------------------------------------

function group_belong_group($id_group_a, $id_groupset){
        // Conexion con la base Datos 
	$childgroup[] = "";
	if ($id_group_a == $id_groupset)
		return 1;
	give_groupchild($id_groupset, $childgroup);
	foreach ($childgroup as $key => $value){
		if (($value != $id_groupset) AND
		    (group_belong_group($id_group_a, $value) == 1))
			return 1;
  	}
	if (array_in ($childgroup, $id_group_a) == 1)
		return 1; 
	else 
		return 0;
}


?>
