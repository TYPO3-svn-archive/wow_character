<html>
<head>
  <title>WOW Character Translation Tool</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
<pre><?php

  libxml_use_internal_errors(false);
  libxml_clear_errors();
  $xml = simplexml_load_file('locallang.xml');

  foreach( $xml->data->languageKey as $languageKey )$lang[strval($languageKey['index'])] = $languageKey;

  print('<form action="" method="POST"><table width="100%">');
  print('<tr><th width="150">Key</th><th width="150">EN</th><th width="150"><input type="text" name="lang" size="2" value="'.$_POST['lang'].'"></th>');
  print('<td width="100%" rowspan="9999">');
  print('Please mail to <a href="mailto:jobe@jobesoft.de?subject=wow_character translation ('.$_POST['lang'].')">jobe@jobesoft.de</a>. Thank you!');
  print('<textarea style="width:100%;height:100%;" tabindex="9999" readonly>');
  print(sprintf('<languageKey index="%s" type="array">'.chr(10),strtolower($_POST['lang'])));
  foreach( $_POST as $key => $value )if($key!='lang')print(sprintf('  <label index="%s">%s</label>'.chr(10),$key,$value));
  print('</languageKey>'.chr(10));
  print('</textarea><td>');
  print('</tr>');
  foreach( $lang['default']->label as $label ){
    print(sprintf('<tr><td>%s</td><td>"%s"</td><td><input type="text" name="%s" value="%s"></td></tr>',$label['index'],$label,$label['index'],$_POST[strval($label['index'])]));
  }
  print('<tr><td colspan="4"><input type="submit"></td></tr>');
  print('</table></form>');
  
?></pre>
</body>
</html>
