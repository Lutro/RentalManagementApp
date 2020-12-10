

$("#data").each(function(index, table) {

    function plot() {

        let data = [ {
            labels: $(table).find("tbody td:nth-child(1)").map((index, element) => { return $(element).html() }).get(),
            values: $(table).find("tbody td:nth-child(2)").map((index, element) => { return $(element).html() }).get(),
            type:'pie',
            text:$(table).find("tbody td:nth-child(1)").map((index, element) => { return $(element).html() }).get()
            
        }]
        console.log(data);
        Plotly.newPlot("tempGraph", data)
    } 

    $("#plot_button").on("click", plot);
    plot();

})

console.log($("#data").find("tbody td:nth-child(2)").map((index, element) => { return $(element).html() }).get());