function getAllData(){

    fetch('ajax/show_all', { // 第1引数に送り先
    })
        .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
        .then(res => {
         /*--------------------
              PHPからの受取成功
             --------------------*/
            // 取得したレコードをeachで順次取り出す
            res.forEach(elm =>{
                var insertHTML = "<div class=\"row border \"><div class=\"col-3\">" + elm['order_use_from'] + "～" + elm['order_use_to'] + "</div><div class=\"col-2 text-bold\"><a href=\"order/detail/" + elm['order_id'] + "\">" + elm['order_no'] + "</a></div><div class=\"col\">" + elm['seminar_name'] + "</div>                <div class=\"col-2\">" + elm['name'] + "</div>                <div class=\"col-2\">" + elm['order_status'] + "</div></div>"
                var all_show_result = document.getElementById("all_show_result");
                all_show_result.insertAdjacentHTML('afterend', insertHTML);
            })
            console.log("通信成功");
            console.log(res); // 返ってきたデータ
        })

        .catch(error => {
            console.log(error); // エラー表示
        })
}

// 関数を実行
getAllData();

