<?php
/* Print numbers from 1 to 100 in which number divisible by 3 replaced with foo
   and number divisible by 5 replaced with bar
   and divisible by both 3 and 5 is replaced by foobar */
   
for ($i=1; $i <=100 ; $i++) {
  if($i % 3 == 0 && $i % 5 ==0){
    echo "foobar,";
    continue;
  }
  if($i % 3 == 0){
    echo "foo,";
    continue;
  }
  if($i % 5 == 0){
    echo "bar,";
    continue;
  }
  echo "$i,";
  continue;
}
?>
