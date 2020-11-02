var myCurrentArray = new Array("apple","banana","apple","orange","banana","apple");
var counts = {};
for(var i=0;i< myCurrentArray.length;i++)
{
  var key = myCurrentArray[i];
  counts[key] = (counts[key])? counts[key] + 1 : 1 ;

}

alert(counts['apple']);
alert(counts['banana']);