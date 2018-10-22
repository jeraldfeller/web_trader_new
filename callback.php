<?php
$post = json_encode($_POST);
$get = json_encode($_GET);


$myfilePost = fopen('tmp/post.txt', "a") or die("Unable to open file!");
                  fwrite($myfilePost, $post);
                  fwrite($myfilePost, PHP_EOL);
                  fwrite($myfilePost, PHP_EOL);
                  fclose($myfilePost);

$myfileGet = fopen('tmp/get.txt', "a") or die("Unable to open file!");
                                    fwrite($myfileGet, $get);
                                    fwrite($myfileGet, PHP_EOL);
                                    fwrite($myfileGet, PHP_EOL);
                                    fclose($myfileGet);

?>
