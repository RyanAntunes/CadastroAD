<?php
session_start();

function authenticate($user, $password) {
    if (empty($user) || empty($password)) return false;

    // Configuração do servidor LDAP
    $adServer = "ldaps://172.17.1.2"; // IP do servidor AD
    $base_dn = "OU=VISITANTES,OU=Usuarios de Servicos,OU=_Jotabasso,DC=jotabasso,DC=com,DC=br"; // DN base onde os usuários estão
    $ldaprdn = 'JOTABASSO' . "\\" . $user; // Formato DOMAIN\user para autenticação

    // Definição do grupo que os usuários devem pertencer
    $ldap_group = "CN=Web_Visitantes,OU=VISITANTES,OU=Usuarios de Servicos,OU=_Jotabasso,DC=jotabasso,DC=com,DC=br";

    // Conectar ao servidor LDAP
    $ldap = ldap_connect($adServer, 636);
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

    // Tentar autenticar o usuário
    $bind = @ldap_bind($ldap, $ldaprdn, $password);

    if ($bind) {
        // Filtrar o usuário específico
        $filter = "(sAMAccountName=$user)";
        $details = array("memberof", "sAMAccountName", "cn");
        $result = ldap_search($ldap, $base_dn, $filter, $details);
        $info = ldap_get_entries($ldap, $result);
        
        $access = 0;

        // Verificar se o usuário pertence ao grupo específico
        if (isset($info[0]['memberof']) && is_array($info[0]['memberof'])) {
            foreach ($info[0]['memberof'] as $grps) {
                if (strpos($grps, $ldap_group) !== false) {
                    $access = 1; // Usuário tem acesso
                    break;
                }
            }
        }

        ldap_unbind($ldap);

        // Se o usuário tem acesso, armazenar as informações na sessão
        if ($access != 0) {
            $_SESSION['displayname'] = $info[0]['cn'][0];
            $_SESSION['user'] = $user;
            $_SESSION['access'] = $access;
            return true;
        } else {
            return false; // Acesso negado
        }
    } else {
        return false; // Falha na autenticação
    }
}
?>
