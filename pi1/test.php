<html>
<head>
  <?
    libxml_use_internal_errors(false); libxml_clear_errors();
    libxml_set_streams_context(stream_context_create(array('http' => array(
      'user_agent' => sprintf('Mozilla/5.0 (Windows; U; Windows NT 5.1; %s; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6',$lang[0]),
      'header' => sprintf('Accept-language: %s-%s',$lang[0],$lang[1]),
    ))));
  ?>
  <title>wow_character - test</title>
</head>
<body>
  <pre style="font:10px monospace;"><h1>TEST 1 - Load local xml</h1><?print_r(simplexml_load_file('test.xml'));?></pre><hr>
  <pre style="font:10px monospace;"><h1>TEST 2 - Load remote file</h1><?file_get_contents("http://armory.wow-europe.com/character-sheet.xml?r=blackhand&n=jobe");print_r($http_response_header);?></pre><hr>
  <pre style="font:10px monospace;"><h1>TEST 3 - Load remote xml</h1><form action="" type="GET">Enter realm:<input type="text" name="realm"> and name:<input type="text" name="name"> and <input type="submit" value="submit"> query.</form><?
    if($_GET['name']&&$_GET['realm']){
      include("class.tx_wowcharacter_pi1_character.php");
      $character = new tx_wowcharacter_pi1_character($_GET['realm'],$_GET['name'],$_GET['lang']);
      print_r($character);
    }
  ?></pre><hr>
</body>
</html>
