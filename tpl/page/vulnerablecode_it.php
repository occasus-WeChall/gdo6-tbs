<?php
use GDO\Forum\GDO_ForumBoard;
use GDO\UI\GDT_Link;

$path = GDO_WEB_ROOT . 'GDO/TBS/vulnerable_code/';
$of = $path . 'overflows/';
$sx = $path . 'scripts/';
$href = GDO_ForumBoard::getBy('board_title', 'Vulnerable Code')->hrefView();
$link = GDT_Link::make()->labelRaw('Vulnerable Code')->href($href)->renderCell()
?>
<div class="module">
  <h1>Try and Learn</h1>
  <h3 style="margin-top:0px;">Introduction</h3>
  <div class="content"><div class="box">
    Qual'è il modo migliore per imparare a identificare e sfruttare vulnerabilità (exploit)?
    Secondo me leggendo tutorial e articoli, MA SOPRATUTTO esercitare ogni cosa letta.
    L'unico problema è che la maggior parte degli script vulnerabili, vengono aggiornati
    frequentemente e le versioni precedenti con il loro tempo scompaiono. Questo è il
    motivo principale per presentarti una lista di web application e porzioni di codice
    che sono realmente vulnerabili. Non rilascio nessuna informazione sulla tipologia
    di exploit... Semplicemente prova e imparara. Studia il codice sorgente
    e non commettere gli stessi errori che questi programmatori hanno fatto!<br />
    Per eventuali domande, c'è il forum <?=$link?>.
  </div></div>
  <h3>PHP Scripts</h3>
  <div class="content"><div class="box">
<ul class="com_list1">
<li><a href="<?=$sx?>webnews_v1.3.zip" target="_blank">WEB//NEWS 1.3 [de]</a></li>
<li><a href="<?=$sx?>phpForum_RC1.zip" target="_blank">phpForum 2 - RC1 [de]</a></li>
<li><a href="<?=$sx?>newsv0.5.zip" target="_blank">IS simple news [en]</a></li>
<li><a href="<?=$sx?>is-shoutv2.zip" target="_blank">IS-shoutv2 [en]</a></li>
<li><a href="<?=$sx?>guestbookv1.2.zip" target="_blank">INLINESHOTS.INFO-GUESBOOK SCRIPT [en]</a></li>
<li><a href="<?=$sx?>board51.zip" target="_blank">Board51 [de]</a></li>
<li><a href="<?=$sx?>mdnews.zip" target="_blank">MD News v1 [en]</a></li>
<li><a href="<?=$sx?>cyphor-0.19.zip" target="_blank">Cyphor Release 0.19 [en]</a></li>
<li><a href="http://www.php-spezial.de/zip/phpvoting-2.5.zip" target="_blank">FotoVoting 1.2 - 2.5 [remote link] [de]</a></li>
</ul>
  </div></div>
  <h3>C Code Snippets: Overflows</h3>
  <div class="content"><div class="box">
<ul class="com_list1">
<li><a href="<?=$of?>strcpy_plain.c" target="_blank">Plain and Simple</a></li>
<li><a href="<?=$of?>strcat_plain.c" target="_blank">Plain and Simple Strcat</a></li>
<li><a href="<?=$of?>abo2.c" target="_blank">Advanced Buffer Overflow #2 by gera</a></li>
<li><a href="<?=$of?>abo3.c" target="_blank">Advanced Buffer Overflow #3 by gera</a></li>
<li><a href="<?=$of?>abo4.c" target="_blank">Advanced Buffer Overflow #4 by gera</a></li>
<li><a href="<?=$of?>abo5.c" target="_blank">Advanced Buffer Overflow #5 by gera</a></li>
<li><a href="<?=$of?>abo6.c" target="_blank">Advanced Buffer Overflow #6 by gera</a></li>
<li><a href="<?=$of?>abo7.c" target="_blank">Advanced Buffer Overflow #7 by gera</a></li>
<li><a href="<?=$of?>abo8.c" target="_blank">Advanced Buffer Overflow #8 by gera</a></li>
<li><a href="<?=$of?>abo9.c" target="_blank">Advanced Buffer Overflow #9 by gera</a></li>
<li><a href="<?=$of?>abo10.c" target="_blank">Advanced Buffer Overflow #10 by gera</a></li>
</ul>
  </div></div>
</div>
