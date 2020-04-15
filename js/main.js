
var wordcloud, size = [700, 300];
var fontSize = d3.scale.log().range([15, 100]);

function loaded() {
	d3.layout.cloud()
		.size(size)
		.words(words)
		.fontSize(function(d) { return fontSize(+d.size); })
		.on("end", draw)
		.start();
}

function draw(words) {
	wordcloud = d3.select("body")
		.append("svg")
			.attr("width", size[0])
			.attr("height", size[1])
		.append("g")
			.attr("transform", "translate(" + (size[0]/2) + "," + (size[1]/2) + ")");
	
	wordcloud.selectAll("text")
			.data(words)
		.enter().append("text")
			.style("font-size", function(d) { return d.size + "px"; })
			.attr("text-anchor", "middle")
			.attr("transform", function(d) {
			return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
			})
			.text(function(d) { return d.text; });
}