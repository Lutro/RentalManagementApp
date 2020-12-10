

$("#data").each(function(index, table) {

    function plot() {

        let data = [ {
            x: $(table).find("tbody td:nth-child(1)").map((index, element) => { return $(element).html() }).get(),
            y: $(table).find("tbody td:nth-child(2)").map((index, element) => { return $(element).html() }).get(),
            type:'bar',
            text:$(table).find("tbody td:nth-child(2)").map((index, element) => { return $(element).html() }).get()
            
        }]
        console.log(data);
        Plotly.newPlot("tempGraph", data)
    } 

    $("#plot_button").on("click", plot);
    plot();

})

console.log($("#data").find("tbody td:nth-child(2)").map((index, element) => { return $(element).html() }).get());