<?php
include_once("php/properties.php");

// initialize the global database object
try {
  $database = new PDO("sqlite:database/properties.sqlite");
  $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
  print "Something went wrong with the database.";
  // if there is actually a persistent error: add output code here to check it
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    extensions: ["tex2jax.js"],
    jax: ["input/TeX", "output/HTML-CSS"],
    tex2jax: {
      inlineMath: [ ['$','$'], ["\\(","\\)"] ],
      displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
      processEscapes: true,
    },
    "HTML-CSS": { availableFonts: ["TeX"], webFont: "Gyre-Pagella" },
    displayAlign: "left"
  });
</script>
<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js"></script>

<link rel="stylesheet" type="text/css" href="css/main.css">

<script type="text/javascript">
// keep track of the location of the mouse and change the position of the preview
$(document).mousemove (function(e) {
  $("div.preview").css({"top": (20 + e.pageY) + "px", "left": (20 + e.pageX) + "px"});
});

// toggle the tag preview for the definition of a concept
function toggleTagView() {
  var tag = $(this).data("tag");

  // the preview doesn't exist yet, hence we create it
  if ($("div#preview-" + tag).length == 0) {
    // create the element
    $("body").append($("<div class='preview' id='preview-" + tag + "'></div>"));
    // load the HTML from the proxy script
    $("div#preview-" + tag).load("php/tag.php?tag=" + tag, function() {
      // render math once the text has been loaded
      MathJax.Hub.Queue(["Typeset", MathJax.Hub, "preview-" + tag]);
    });
  }
  // otherwise we can just toggle its visibility
  else
    $("div#preview-" + tag).toggle();
}

$(document).ready(function() {
  // hovering over a property shows its definition
  $("th[data-tag]").hover(toggleTagView);
});
</script>

</head>

<body>

<h1>Stability of properties</h1>

<table>
<thead>
<tr>
  <th></th>
<?php
$situations = getSituations();
foreach ($situations as $situation)
  print "<th>" . $situation["name"] . "</th>";
?>
</tr>
</thead>

<tbody>
<?php
$properties = getProperties();
$relations = getRelations();
foreach ($properties as $property) {
  print "<tr>";
  print "<th data-tag='" . $property["tag"] . "'><a href='http://stacks.math.columbia.edu/tag/" . $property["tag"] . "'>" . $property["name"] . "</a></th>";

  foreach ($situations as $situation) {
    if ($relations[$property["name"]][$situation["name"]] == "")
      print "<td></td>";
    else
      print "<td data-tag='" . $relations[$property["name"]][$situation["name"]] . "'><a href='http://stacks.math.columbia.edu/tag/" . $relations[$property["name"]][$situation["name"]] . "'>&#x2713;</a></td>";
  }

  print "</tr>";
}
?>
</tbody>
</table>
</body>
</html>
