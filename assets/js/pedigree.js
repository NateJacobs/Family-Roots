var margin = {top: 0, right: 320, bottom: 0, left: 0},
    width = 960 - margin.left - margin.right,
    height = 600 - margin.top - margin.bottom;

var tree = d3.layout.tree()
    .separation(function(a, b) { return a.parent === b.parent ? 1 : .5; })
    .children(function(d) { return d.parents; })
    .size([height, width]);

var svg = d3.select("#pedigree-chart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
	.append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var json = jQuery.parseJSON(jQuery('#person-pedigree-json').text());

function buildTree (json) {
  var nodes = tree.nodes(json);

  var link = svg.selectAll(".pedigree-link")
      .data(tree.links(nodes))
	  .enter().append("path")
      .attr("class", "pedigree-link")
      .attr("d", elbow);

  var node = svg.selectAll(".node")
      .data(nodes)
    .enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })

  node.append("text")
      .attr("class", "pedigree-name")
      .attr("x", 8)
      .attr("y", -6)
      .text(function(d) { return d.name; });

  node.append("text")
      .attr("x", 8)
      .attr("y", 8)
      .attr("dy", ".71em")
      .attr("class", "pedigree-about lifespan")
      .text(function(d) { return d.born + " – " + d.died; });

  node.append("text")
      .attr("x", 8)
      .attr("y", 8)
      .attr("dy", "1.86em")
      .attr("class", "pedigree-about location")
      .text(function(d) { return "Born: " + d.birthPlace; });
      
  node.append("text")
      .attr("x", 8)
      .attr("y", 22)
      .attr("dy", "1.86em")
      .attr("class", "pedigree-about location")
      .text(function(d) { return "Died: " + d.deathPlace; });
};

function elbow(d, i) {
  return "M" + d.source.y + "," + d.source.x
       + "H" + d.target.y + "V" + d.target.x
       + (d.target.children ? "" : "h" + margin.right);
}

buildTree(json);