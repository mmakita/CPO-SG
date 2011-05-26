<script type="text/javascript" src="scripts/commom.js"></script>
<script type="text/javascript" src="scripts/doc_novo.js"></script>

<br />
<form id="novoForm" action="sgd.php?acao=salvar" method="post" enctype="multipart/form-data">
<table width="100%">
<tr><td width="60%">
	{$campos}
</td><td width="40%" style="padding-left:20px;">
	{$emitente}<br /><br />
	{$documentos}
	{$obra}
	{$empresa}
</td></tr>

<tr><td colspan="2"><b>Anexar Arquivo:</b></td></tr>
<tr><td colspan="2">{$anexarArq}</td></tr>
<tr><td colspan="2"><b>Despacho:</b></td></tr>
<tr><td colspan="2">{$despacho}</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="Enviar"></td></tr>
</table>
</form>