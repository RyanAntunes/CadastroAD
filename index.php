<?php 
session_start();

include ('navigation.php');

if(isset($_GET['registered'])) {

    if(isset($_POST['fname'])) {

        $usuario = "";
        $senha = '';

        $patrocinador = $_SESSION['user'];
        
        $nome = $_POST['fname'];
        $sobrenome = $_POST['lname'];
        $nomeCompleto = $nome . " " . $sobrenome;
        $objetivo = $patrocinador . ": " . $_POST['purpose'];
        $localizacao = $_POST['location'];
        $usuarioVisitante = $_POST['guestusername'];
        
        $conta = $usuarioVisitante;
        
        $senhaTexto = $_POST['guestpassword'];
        $dnVisitante = "CN=" . $nomeCompleto . ",";
        
        $novaSenha = "\"" . $senhaTexto . "\"";
        $tamanho = strlen($novaSenha);
        $novaSenhaFormatada = "";
        
        for ($i = 0; $i < $tamanho; $i++) {
            $novaSenhaFormatada .= $novaSenha[$i] . "\000";
        }

        $tempoAtualUnix = time(); 
        $segundosEntre1601e1970 = 11644473600;
        
        if ($_POST['length'] != "1 Hora"){
            $segundosPorDia = "86400";
            $segundosDeExpiracao = $_POST['length'] * $segundosPorDia;
        } else {
            $segundosDeExpiracao = "3600";
        }
        
        $tempoAdicionado = $tempoAtualUnix + $segundosEntre1601e1970 + $segundosDeExpiracao;
        $nanosegundos = $tempoAdicionado * 10000000; 

        $dt = new DateTime('now + '.$_POST['length']);
        
        $adServer = "ldaps://";
        $base_dn = "DC=dominio,DC=com";
        $ldaprdn = '' . "\\" . $usuario;
        $ldap_user_group = "";
        $ldap_manager_group = "";

        $ldap = ldap_connect($adServer, 636);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($ldap, $ldaprdn, $senha);

        $filtro = "(sAMAccountName=$conta)";
        $detalhes = array("sAMAccountName");
        $resultado = ldap_search($ldap, $base_dn, $filtro, $detalhes);

        $entradas = ldap_get_entries($ldap, $resultado);

        usort($entradas['entries'], function($a, $b) {
            return strcmp($a['sn'][0], $b['sn'][0]);
        });

        $info = count($entradas['entries']);

        ldap_close($ldap);

        if ($info != 1) {
            $ldap = ldap_connect($adServer, 636);
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
            $bind = @ldap_bind($ldap, $ldaprdn, $senha);

            $registroLdap['cn'] = $nomeCompleto;
            $registroLdap['givenName'] = $nome;
            $registroLdap['sn'] = $sobrenome;
            $registroLdap['objectclass'][0] = "top";
            $registroLdap['objectclass'][1] = "person";
            $registroLdap['objectclass'][2] = "organizationalPerson";
            $registroLdap['objectclass'][3] = "user";
            $registroLdap["accountExpires"] = $nanosegundos;
            $registroLdap["description"] = $objetivo;
            $registroLdap["physicalDeliveryOfficeName"] = $localizacao;
            $registroLdap["unicodepwd"] = $novaSenhaFormatada;
            $registroLdap["sAMAccountName"] = "hpsgst_" . $usuarioVisitante;
            $registroLdap["UserAccountControl"] = "512"; 

            $r = ldap_add($ldap, $dnVisitante, $registroLdap);
            
            ldap_close($ldap);

            $ldap = ldap_connect($adServer, 636);
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
            $bind = @ldap_bind($ldap, $ldaprdn, $senha);

            $nomeGrupo = "";
            $infoGrupo["member"] = $dnVisitante;
            $adicaoGrupo = ldap_mod_add($ldap, $nomeGrupo, $infoGrupo);
            
            ldap_close($ldap);
        ?>
        
        <div class="container"> 
            <div class="alert alert-success no-print" role="alert">
                Seu convidado foi registrado com sucesso. Por favor, compartilhe ou imprima as informações abaixo para o seu convidado. Seu convidado poderá usar o mesmo nome de usuário e senha abaixo para acessar a rede sem fio, bem como fazer login em qualquer computador do distrito.
            </div>

            <div class="alert alert-warning no-print" role="alert"> Ao registrar seu convidado, você assume total responsabilidade por suas ações enquanto estiver usando a rede sem fio e/ou computador(es) do distrito.<br />
                Além disso, ao usar esta conta e conectar-se à rede sem fio, seu convidado concorda em aceitar e cumprir os termos estabelecidos na Política de Uso Aceitável.
            </div>
        </div>   
        <div class="container">
            <div class="form-group row">
                <div class="col-xs-3">
                    <strong>Nome do Convidado:</strong>
                </div>
                <div class="col-xs-8">
                    <?php echo $nome . " " . $sobrenome; ?> 
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-3">
                    <strong>Localização do Convidado:</strong>
                </div>
                <div class="col-xs-8">
                    <?php echo $localizacao; ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-3">
                    <strong>Objetivo do Acesso:</strong>
                </div>
                <div class="col-xs-8">
                    <?php echo $_POST['purpose']; ?> 
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-3">
                    <strong>Patrocinador:</strong>
                </div>
                <div class="col-xs-8">
                    <?php echo $patrocinador; ?> 
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-3">
                    <strong>login:</strong>
                </div>
                <div class="col-xs-8">
                    <?php echo $conta; ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-3">
                    <strong>Senha do Convidado:</strong>
                </div>
                <div class="col-xs-8">
                    <?php echo $senhaTexto; ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-3">
                    <strong>Expiração:</strong>
                </div>
                <div class="col-xs-8">
                    Esta conta expirará em: <?php echo $dt->format('d-m-Y H:i:s') ?>
                </div>
            </div>
            <div class="alert alert-info" role="alert"> 
                Se você tiver alguma dúvida, preocupação ou precisar de mais assistência, por favor, envie um chamado para o suporte ou ligue para a linha de suporte na extensão 5511.
            </div>
            <button type="button" class="btn btn-primary no-print" onclick="printme()"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp;Imprimir</button>

            <script>
                function printme() {
                    window.print();
                }
            </script>
        </div>

        <?php

        } else {

        ?>
        <div class="container"> 
            <div class="alert alert-danger no-print" role="alert">
                Erro: Este nome de usuário já existe. Por favor, tente novamente com um nome de usuário diferente.
            </div>
        </div>

        <?php

        }
    }
} else {
    $acesso = $_SESSION['access'] ?? null;
    $nomeExibicao = $_SESSION['displayname'] ?? "Usuário";

?>

    <div class="container" style="clear:both;">
        <h2><?php echo "Bem-vindo, $nomeExibicao"; ?></h2>
        <div class="alert alert-warning" role="alert">
            <p>Notificação de alerta</p>
        </div>
    </div><br />

    <!-- Formulário de Registro -->
    <div class="container">
        <form action="index.php?registered" method="post">
            <div class="form-group row">
                <label for="fname" class="col-sm-2 form-control-label">Nome do Convidado:</label>
                <div class="col-sm-10">
                    <input type="text" name="fname" placeholder="Nome do Convidado" required autofocus="autofocus" class="form-control"/>
                </div>
            </div>
            <div class="form-group row">
                <label for="lname" class="col-sm-2 form-control-label">Sobrenome do Convidado:</label>
                <div class="col-sm-10">
                    <input type="text" name="lname" placeholder="Sobrenome do Convidado" required autofocus="autofocus" class="form-control"/>
                </div>
            </div>
            <div class="form-group row">
                <label for="purpose" class="col-sm-2 form-control-label">Objetivo do Acesso:</label>
                <div class="col-sm-10">
                    <input type="text" name="purpose" placeholder="Objetivo do Acesso" required autofocus="autofocus" class="form-control"/>
                </div>
            </div>
            <div class="form-group row">
                <label for="location" class="col-sm-2 form-control-label">Localização do Convidado:</label>
                <div class="col-sm-10">
                    <input type="text" name="location" placeholder="Localização do Convidado" required autofocus="autofocus" class="form-control"/>
                    <small class="text-muted">Localização primária para referência. Seu convidado não está limitado a usar sua conta neste local.</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="length" class="col-sm-2 form-control-label">Duração do Acesso:</label>
                <div class="col-sm-10">
                    <select name="length" value='' class="form-control">
                        <option value="1 Hour">1 Hora</option>                                                       
                        <option value="1 Day">1 Dia</option>
                        <option value="2 Days">2 Dias</option>
                        <option value="3 Days">3 Dias</option>
                        <option value="4 Days">4 Dias</option>
                        <option value="5 Days">5 Dias</option>
                        <?php if ($acesso == 2) {
                            echo '<option value="30 Days">1 Mês</option>
                            <option value="60 Days">2 Meses</option>
                            <option value="180 Days">6 Meses</option>
                            <option value="365 Days">1 Ano</option>';
                        } else {
                            echo '<option id="disabledInput" value="" disabled>Por favor, envie um chamado ao suporte para durações mais longas.</option>';
                        }
                        ?>
                    </select>
                    <small class="text-muted">A conta do seu convidado será automaticamente desativada ao final da duração solicitada.</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="guestusername" class="col-sm-2 form-control-label">Nome de Usuário do Convidado:</label>
                <div class="col-sm-10">
                    <input type="input" name="guestusername" placeholder="Nome de Usuário Desejado" required autofocus="autofocus" class="form-control" maxlength="13"/>
                    <small class="text-muted">Máximo de 13 caracteres. Todas as contas de convidados terão um prefixo hpsgst_ adicionado automaticamente.</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="location" class="col-sm-2 form-control-label">Senha do Convidado:</label>
                <div class="col-sm-10">
                    <input type="password" name="guestpassword" placeholder="Senha Desejada" minlength="5" autofocus="autofocus" class="form-control" />
                    <small class="text-muted">Mínimo de 5 caracteres.</small>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-offset-2 col-sm-10">
                    <button class="btn btn-primary" type="submit">Registrar Convidado</button>
                </div>
            </div>
        </form>
    </div>

<?php } ?>

</div>

<!-- JavaScript do Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="css/jquery.min.js"><\/script>')</script>
<script src="css/bootstrap.min.js"></script>
<script src="css/ie10-viewport-bug-workaround.js"></script>
</body>
