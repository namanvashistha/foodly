function add_item(cur_id){
            var quan=document.getElementById(cur_id).innerHTML;
            if(quan<10)
                document.getElementById(cur_id).innerHTML=++quan;
        }
        function remove_item(cur_id){
            var quan=document.getElementById(cur_id).innerHTML;
            if(quan>0)
                document.getElementById(cur_id).innerHTML=--quan;
        }
        function view_cart(n){
            var j=0;
            var str="?";
            for (var i=0;i<n;i++) {
                var nam = document.getElementsByClassName("buy")[i];
                var quant=nam.innerHTML;
                var name=nam.id;
                if(quant>0) {
                    str+="item"+j+"="+name+"&quantity"+j+"="+quant+"&";
                    j++;
                }
            }
            str+="count="+j;
            if(j>0)
            window.location.href = "view_cart.php"+str;
        }