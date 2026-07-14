$(function(){

    if($("#slider-range").length){

        let min = Number($("#min_price").val()) || 0;
        let max = Number($("#max_price").val()) || 1000000;

        const url = new URL(window.location);

        if(url.searchParams.get("min_price"))
            min = Number(url.searchParams.get("min_price"));

        if(url.searchParams.get("max_price"))
            max = Number(url.searchParams.get("max_price"));

        $("#slider-range").slider({
            range:true,
            min:0,
            max:1000000,
            step:1000,
            values:[min,max],
            slide:function(event,ui){
                // minimal selisih Rp1000
                if(ui.values[1]-ui.values[0]<1000){
                    return false;
                }

                $("#amount").val(
                    "Rp"+ui.values[0].toLocaleString("id-ID")
                    +" - Rp"+
                    ui.values[1].toLocaleString("id-ID")
                );

                $("#min_price").val(ui.values[0]);
                $("#max_price").val(ui.values[1]);
            }
        });

        $("#amount").on("change",function(){

            let text=$(this).val();

            let arr=text.split("-");

            if(arr.length!==2) return;

            let min=parseInt(arr[0].replace(/\D/g,""));
            let max=parseInt(arr[1].replace(/\D/g,""));

            if(isNaN(min)||isNaN(max)) return;

            if(min>max){
                [min,max]=[max,min];
            }

            $("#slider-range").slider("values",[min,max]);

            $("#min_price").val(min);
            $("#max_price").val(max);

        });

        $("#amount").val(
            "Rp"+$("#slider-range").slider("values",0).toLocaleString("id-ID")
            +" - "
            +"Rp"+$("#slider-range").slider("values",1).toLocaleString("id-ID")
        );

        $("#min_price").val(
            $("#slider-range").slider("values",0)
        );

        $("#max_price").val(
            $("#slider-range").slider("values",1)
        );
    }
});