<?php

session_start();
 
function authenticate($user, $password) {
	if(empty($user) || empty($password)) return false;
 
	$adServer = "ldaps://";

	
	
	
	

	$base_dn = "";


    
	$ldaprdn = '' . "\\" . $user;
					
	
	$ldap_user_group = "";
					
					

	$ldap_manager_group = "";

					
	

	$ldap_staff_group= "";
	
	
	
   

    $ldap = ldap_connect($adServer, 636);
	 ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

	 $bind = @ldap_bind($ldap, $ldaprdn, $password);

	 if ($bind) {
		$filter = "(sAMAccountName=$user)";
		$details = array("memberof", "sAMAccountName", "cn");
		$result = ldap_search($ldap, $base_dn, $filter, $details);
	
		// Obter entradas de resultados
		$info = ldap_get_entries($ldap, $result);
	
		// Ordenar entradas pelo atributo 'sn' (sobrenome) se existir
		if (isset($info[0]['sn'])) {
			usort($info, function($a, $b) {
				return strcmp($a['sn'][0], $b['sn'][0]);
			});
		}
	
		ldap_unbind($ldap);
	
		$access = 0;
	
		if ($bind) {
			$filter = "(sAMAccountName=$user)";
			$details = array("memberof", "sAMAccountName", "cn");
			$result = ldap_search($ldap, $base_dn, $filter, $details);
		
			// Obter entradas de resultados
			$info = ldap_get_entries($ldap, $result);
		
			// Certifique-se de que todas as variáveis estão definidas
			if (!isset($ldap_manager_group)) {
				$ldap_manager_group = ''; // Valor default ou obtenha o valor apropriado
			}
			if (!isset($ldap_tek_group)) {
				$ldap_tek_group = ''; // Valor default ou obtenha o valor apropriado
			}
			if (!isset($ldap_staff_group)) {
				$ldap_staff_group = ''; // Valor default ou obtenha o valor apropriado
			}
		
			// Verificar se o atributo 'memberof' está presente e é um array
			if (isset($info[0]['memberof']) && is_array($info[0]['memberof'])) {
				$access = 0;
				foreach ($info[0]['memberof'] as $grps) {
					if (strpos($grps, $ldap_manager_group) !== false) {
						$access = 3;
						break;
					} elseif (strpos($grps, $ldap_tek_group) !== false) {
						$access = 2;
						break;
					} elseif (strpos($grps, $ldap_staff_group) !== false) {
						$access = 1;
					}
				}
			} else {
				// Caso o atributo 'memberof' não esteja presente ou não seja um array
				$access = 0; // Ou outro valor default
			}
		
			ldap_unbind($ldap);
		}
		
	}
	
		

		foreach($info[0]['cn'] as $cns) {
			
			$displayname = $cns;
 
			
		 	
		
		}
		
		
		
 
		if($access != 0) {

			$_SESSION['displayname'] = $displayname;
			$_SESSION['user'] = $user;
			$_SESSION['access'] = $access;
			return true;
		} else {

			return false;
		}
 
	  
	}
?>