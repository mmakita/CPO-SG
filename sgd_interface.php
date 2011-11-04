<?php
function showAtribuirObraTemplate() {
	return array('template' => '
		<span id="obraAtual" style="font-weight: bold">{$obraAtual}</span><br /><br />
		Atribuir este documento &agrave; obra:
		<input type="text" class="obrigatorio" name="nome" id="nome" size="50" maxlength="250" autocomplete="off" />
		<div id="sugestoesObra" style="padding: 3px; margin: 2px; border: 1px #BE1010 solid; display: none;"></div>
	',
	'sem_obra' => 'Este documento n&atilde;o est&aacute; relacionado a nenhuma obra.',
	'com_obra' => 'Este documento est&aacute; realcionado &agrave; obra: <a href="javascript:void(0)" onclick="javascript:window.open(\'sgo.php?acao=ver&amp;obraID={$obra_id}\',\'obra_det\',\'width=900,height=650,scrollbars=yes,resizable=yes\')">{$obra_nome}</a>');
}
?>