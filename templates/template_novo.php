<script type="text/javascript" src="scripts/commom.js"></script>
<script type="text/javascript" src="scripts/doc_novo.js"></script>

<br />
<form id="novoForm" action="sgd.php?acao=salvar" method="post" enctype="multipart/form-data">
<table width="100%">
<tr><td width="60%">
	{$campos}
<<<<<<< HEAD
</td>
<td width="15%" style="padding-left:20px;">
	{$emitente}
=======
</td><td width="40%" style="padding-left:20px;">
	{$emitente}<br /><br />
	{$documentos}
	{$obra}
	{$empresa}
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
</td></tr>
<tr><td colspan="2"><b>Anexar Arquivo:</b></td></tr>
<tr><td colspan="2">{$anexarArq}</td></tr>
<tr><td colspan="2"><b>Despacho:</b></td></tr>
<tr><td colspan="2">{$despacho}</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="Enviar"></td></tr>
</table>
</form>