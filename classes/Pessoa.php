<?php
/**
 * @version 0.9 (13/5/2011)
 * @package geral
 * @author Mario Akita
 * @desc contem a classe Pessoa e os metodos relativos ao gerenciamento de usuarios.
 */

/**
 * @package geral
 * @subpackage classes
 * @desc lida com o gerenciamento de usuarios (adicao, remocao, leitura dos dados de usuarios, autenticacao
 */

class Pessoa{
	/**
	 * nome de usuario
	 * @var string
	 */
	private $username;
	
	private $matricula;
	
	private $descr;
	
	/**
	 * nome completo do usuario
	 * @var string
	 */
	private $nome;
	
	/**
	 * email do usuario
	 * @var string
	 */
	private $email;
	
	/**
	 * area do usuario
	 * @var string
	 */
	private $area;
	
	/**
	 * cargo do usuario
	 * @var string
	 */
	private $cargo;
	
	/**
	 * grupo do usuario
	 * @var string
	 */
	private $grupo;
	
	/**
	 * permissoes do usuario
	 * @var array
	 */
	private $perm;
	
	/**
	 * indica se o usuario esta ativo ou ja foi desligado
	 * @var boolean
	 */
	private $ativo;
	
	/**
	 * chave se sessao do usuario
	 * @var string
	 */
	private $chave;
	
	/**
	 * identifica se o usuario esta logado ou nao
	 * @var boolean
	 */
	private $logado;
	
	private $id;
	
	/**
	 * @desc inicia uma nova variavel com valores nulos
	 */
	public function __construct() {
		//session_start();
		
		$this->setNull();
		//loga com os dados da sessao ou seta todas as variaveis = null
		if(isset($_SESSION['username'])){
			$this->id        = $_SESSION['id'];
			$this->username  = $_SESSION['username'];
			$this->nome      = $_SESSION['nome'];
			$this->email     = $_SESSION['email'];
			$this->area      = $_SESSION['area'];
			$this->cargo     = $_SESSION['cargo'];
			$this->descr     = $_SESSION['descr'];
			$this->grupo     = $_SESSION['grupo'];
			$this->perm      = $_SESSION['perm'];
			$this->ativo     = $_SESSION['ativo'];
			$this->logado    = $_SESSION['logado'];
			$this->matricula = $_SESSION['matricula'];
		}
	}
	
	/**
	 * @desc efetua login e preenche os atributos da classe com os dados do usuario
	 * @return true se login foi bem sucedido, false caso contrario
	 */
	public function login($username, $senha, $bd){
		require_once('adLDAP/adLDAP.php');
		//vetor de dados do usuario
		$user = null;
		
		//se true, ignora autenticacao e prossegue com login (AD Down ou debug)
		$AD_override = false; 
		
		//modo normal de autenticacao
		if(!$AD_override){
			try {
				//inicia conexao com adLDAP
				$adldap = new adLDAP();
				//realiza autenticacao
				if($adldap->authenticate($username, $senha)){
					//verifica se o usuario existe no AD e pega os dados
					$userdataAD = $adldap->user_info($username,array('displayname','samAccountName','sn','GivenName','userPrincipalName','telephoneNumber','mail','title','department','description','initials','AccountDisabled','enabled'));
					//pega os dados salvos no BD
					$userdataBD = $this->getUserData($username,$bd);
					//atualiza os dados do usuario no BD
					$this->updateUserData($userdataAD,$userdataBD,$bd);
					
					//pega os dados salvos no BD novamente para refletir qualquer mudanca
					$userdataBD = $this->getUserData($username,$bd);
					
					//seta array de dados
					if(isset($userdataBD[0])){
						$user['username']  = $userdataBD[0]['username'];
						$user['nome']      = $userdataBD[0]['nome'];
						$user['sobrenome'] = $userdataBD[0]['sobrenome'];
						$user['nomeCompl'] = $userdataBD[0]['nomeCompl'];
						$user['email']     = $userdataBD[0]['email'];
						$user['area']      = $userdataBD[0]['area'];
						$user['cargo']     = $userdataBD[0]['cargo'];
						$user['matricula'] = $userdataBD[0]['matr'];
						$user['descr']     = $userdataBD[0]['descr'];
						$user['id']        = $userdataBD[0]['id'];
						$user['grupo']     = $userdataBD[0]['gid'];
						$user['perm']      = $this->getPermission($user['grupo'],$bd);
						$user['ativo']     = true;
						$user['logado']    = true;
					}
				}
			} catch (Exception $e) {
				//se pegar excecao, loga no BD
				doLog('ERROAD', 'ERRO AD: '. $e->getMessage(), $bd);
				//mostra erro de falha de conexao com o BD
				showError(13,'login.php');
				exit();
			}
		
		//modo logar sem autenticacao (debug ou AD Down)
		} else {
			//pega os dados salvos no BD
			$userdataBD = $this->getUserData($username,$bd);
			//seta array de dados
			if(isset($userdataBD[0])){
				$user['username']  = $userdataBD[0]['username'];
				$user['nome']      = $userdataBD[0]['nome'];
				$user['sobrenome'] = $userdataBD[0]['sobrenome'];
				$user['nomeCompl'] = $userdataBD[0]['nomeCompl'];
				$user['email']     = $userdataBD[0]['email'];
				$user['area']      = $userdataBD[0]['area'];
				$user['cargo']     = $userdataBD[0]['cargo'];
				$user['matricula'] = $userdataBD[0]['matr'];
				$user['descr']     = $userdataBD[0]['descr'];
				$user['id']        = $userdataBD[0]['id'];
				$user['grupo']     = $userdataBD[0]['gid'];
				$user['perm']      = $this->getPermission($user['grupo'],$bd);
				$user['ativo']     = true;
				$user['logado']    = true;
			}
		}
		
		
		
		//se o usuario se logou com sucesso, loga e retorna true
		if($user['logado']){
			$_SESSION = $user;
			doLog($_SESSION['username'],'Efetuou login.',$bd);
			return TRUE;
		}else{
			//senao, retorna false
			return FALSE;
		}
	}
	
	/**
	 * @desc efetua logout do usuario e encerra a sessao
	 * @return true se bem sucedido e false caso contrario
	 */
	public function logout($bd) {
		//loga que o usuario saiu
		doLog($_SESSION['username'],'Efetuou logout.',$bd);
		//destroi a sessao
		session_destroy();
		//seta o usuario como null
		$this->setNull();
		//retorna o sucesso da operacao
		return TRUE;
	}
	
	/**
	 * @desc seta todas as variaveis como null e logado como false
	 * @uses $this->username, $this->nome, $this->email, $this->area, $this->cargo, $this->grupo, $this->permissoes, $this->ativo, $this->logado
	 */
	private function setNull() {
			$this->id		 = null;
			$this->username  = null;
			$this->nome      = null;
			$this->sobrenome = null;
			$this->nomeCompl = null;
			$this->email     = null;
			$this->area      = null;
			$this->cargo     = null;
			$this->matricula = null;
			$this->descr     = null;
			$this->userID    = null;
			$this->grupo     = null;
			$this->perm      = null;
			$this->ativo     = false;
			$this->logado    = false;
	}
	/**
	 * @desc consulta os dados do usuario no AD
	 * @param string $username
	 * @param musql_link $bd
	 */
	public function getUserData($username,$bd) {
		//seleciona o usuario
		$res = $bd->query("SELECT * FROM usuarios WHERE username = '$username'");
		return $res;
	}
	
	/**
	 * @desc atualiza os dados do usuario se houver necessidade
	 * @param array $ADdata
	 * @param uarray $BDdata
	 * @param mysql_link $bd
	 */
	public function updateUserData($ADdata,$BDdata,$bd){
		//seleciona o primeiro usuario retornado pelo AD
		$ADdata = $ADdata[0];
		//
		if (!count($BDdata)){			
			if(isset($ADdata['initials'][0])
			&& isset($ADdata['samaccountname'][0])
			&& isset($ADdata['givenname'][0]) 
			&& isset($ADdata['sn'][0]) 
			&& isset($ADdata['title'][0]) 
			&& isset($ADdata['department'][0])
			&& isset($ADdata['telephonenumber'][0])
			&& isset($ADdata['description'][0])
			&& isset($ADdata['mail'][0])){
				$sql = "INSERT INTO usuarios (matr,username,gid,nome,sobrenome,nomeCompl,cargo,area,ramal,email,descr) VALUES ("
					."'".$ADdata['initials'][0]."',"
					."'".htmlentities($ADdata['samaccountname'][0],ENT_QUOTES,'UTF-8')."',"
					."'0',"
					."'".htmlentities($ADdata['givenname'][0],ENT_QUOTES,'UTF-8')."',"
					."'".htmlentities($ADdata['sn'][0],ENT_QUOTES,'UTF-8')."',"
					."'".htmlentities($ADdata['displayname'][0],ENT_QUOTES,'UTF-8')."',"
					."'".htmlentities($ADdata['title'][0],ENT_QUOTES,'UTF-8')."',"
					."'".htmlentities($ADdata['department'][0],ENT_QUOTES,'UTF-8')."',"
					."'".htmlentities($ADdata['telephonenumber'][0],ENT_QUOTES,'UTF-8')."',"
					."'".$ADdata['mail'][0]."',"
					."'".htmlentities($ADdata['description'][0],ENT_QUOTES,'UTF-8')."'"
					.")";
				$bd->query($sql);
				doLog($ADdata['samaccountname'][0], "Dados de usuario inseridos no BD", $bd);
			}
		} else {
			$BDdata = $BDdata[0];
			$CAtualizados = '';
			if (htmlentities($ADdata['givenname'][0],ENT_QUOTES,'UTF-8') != $BDdata['nome']){
				$bd->query("UPDATE usuarios SET nome='".htmlentities($ADdata['givenname'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Nome";
			}
			if (htmlentities($ADdata['sn'][0],ENT_QUOTES,'UTF-8') != $BDdata['sobrenome']){
				$bd->query("UPDATE usuarios SET sobrenome='".htmlentities($ADdata['sn'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Sobrenome";
			}
			if (htmlentities($ADdata['displayname'][0],ENT_QUOTES,'UTF-8') != $BDdata['nomeCompl']){
				$bd->query("UPDATE usuarios SET nomeCompl='".htmlentities($ADdata['displayname'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Nome Completo";
			}
			if (htmlentities($ADdata['title'][0],ENT_QUOTES,'UTF-8') != $BDdata['cargo']){
				$bd->query("UPDATE usuarios SET cargo='".htmlentities($ADdata['title'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Cargo";
			}
			if (htmlentities($ADdata['department'][0],ENT_QUOTES,'UTF-8') != $BDdata['area']){
				$bd->query("UPDATE usuarios SET area='".htmlentities($ADdata['department'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Area";
			}
			if (htmlentities($ADdata['telephonenumber'][0],ENT_QUOTES,'UTF-8') != $BDdata['ramal']){
				$bd->query("UPDATE usuarios SET ramal='".htmlentities($ADdata['tlephonenumber'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Ramal";
			}
			if (htmlentities($ADdata['mail'][0],ENT_QUOTES,'UTF-8') != $BDdata['email']){
				$bd->query("UPDATE usuarios SET email='".htmlentities($ADdata['mail'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Email";
			}
			if (htmlentities($ADdata['description'][0],ENT_QUOTES,'UTF-8') != $BDdata['descr']){
				$bd->query("UPDATE usuarios SET descr='".htmlentities($ADdata['description'][0],ENT_QUOTES,'UTF-8')."' WHERE username = '".$BDdata['username']."'");
				$CAtualizados .= " Desc";
			}
			if($CAtualizados)
				doLog($ADdata['samaccountname'][0], "Dados do usuario atualizados no BD:".$CAtualizados, $bd);
		}
		return true;
	}
	
	/**
	 * @desc retorna array de permissoes para o usuario
	 * @param int $gid
	 */
	function getPermission($gid,$bd = null) {
		if($bd == null) {
			global $bd;
		}
		//seleciona as permissoes do grupo ao qual o usuario pertence
		$res = $bd->query("SELECT id,G$gid FROM label_acao");
		//inicializa o vetor
		$perm[0] = 0;
		//se houver o grupo no BD
		if(count($res)) {
			foreach ($res as $r) {
				//pega a permissao para a acao i
				$perm[$r['id']] = $r["G$gid"];
			}
			return $perm;
		}
		else return null;
	}
}

?>