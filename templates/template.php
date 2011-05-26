<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-type" content="text/html;charset=iso-8859-15" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Cache-Control" content="no-cache" />
  <meta http-equiv="Pragma-directive" content="no-cache" />
  <meta http-equiv="Cache-Directive" content="no-cache" />
  <meta http-equiv="Expires" content="1" />
  <link rel="stylesheet" type="text/css" href="css/geral.css" />
  <link rel="stylesheet" type="text/css" href="css/layout.css" />
  <script type="text/javascript" src="scripts/jquery.js"></script>
  <script type="text/javascript" src="scripts/menu.js"></script>
  <script type="text/javascript" src="scripts/commom.js"></script>
  <script type="text/javascript" src="scripts/ajuda.js"></script>
  {$head}
  <title>{$title}</title>
</head>
<body>
  <div id="container">
    <div id="header">
      <h3>{$header}</h3>
    </div>
    <div id="conteudo">
      <div class="boxLeft">
        {$menu}
      </div>

      <div class="boxRight">

        <div class="boxCont">
          <table width="100%">
            <tr><td>{$path}</td><td><span class="par">Bem-vindo(a), {$user} <a href="{$logout_page}">[sair]</a></span></td></tr>
          </table>
        </div>

        <div class="boxCont" id="c1">
          {$content1}
        </div>
      </div>
    </div>
    <div id="footer">
      <hr />
      <table border="0" width="100%">
      <tbody>
      <tr>
        <td width="33%"><span class="footer">{$footer} </span></td>
        <td width="33%" style="text-align: center;">{$campos_admLink}</td>
        <td width="33%" style="text-align: right;"><span class="footer">{$campos_codPag}</span></td>
      </tr>
      </tbody>
      </table>
          
    </div>
  </div>

  
</body>
</html>