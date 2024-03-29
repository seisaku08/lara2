$(function(){

function getAllData(){

    fetch('ajaxpctool/show_all', { // 第1引数に送り先
    })
        .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
        .then(res => {
         /*--------------------
              PHPからの受取成功
             --------------------*/
            // 取得したレコードをeachで順次取り出す
            res.forEach(elm =>{
                var insertHTML = "<tr class=\"target result\"><td>"
                  +  "</td><td>" 
                  + elm['machine_id'] + "</td><td><a href=\"pctool/detail/"
                  + elm['machine_id'] + "\" target=\"_blank\">" 
                  + elm['machine_name'] + "</a></td><td>" 
                  + elm['machine_spec'] + "</td><td>" 
                  + elm['machine_since'] + "</td><td>" 
                  + elm['machine_os'] + "</td><td>" 
                  + elm['machine_cpu'] + "</td><td>" 
                  + elm['machine_memory'] + "</td><td>" 
                  + elm['machine_monitor'] + "</td><td>" 
                  + elm['machine_powerpoint'] + "</td><td>" 
                  + elm['machine_camera'] + "</td><td>" 
                  + elm['machine_hasdrive'] + "</td><td>" 
                  + elm['machine_connector'] + "</td><td>" 
                  + elm['machine_canto11'] + "</td><td>" 
                  + elm['machine_memo'] + "</td></tr>"
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
    

    ajax_seminar_day.addEventListener('change', () => {
        console.log("イベント発火:seminar_day");

        let seminar_day = document.getElementById('ajax_seminar_day').value;

       /*--------------------
            POST送信
        -------------------*/
        const postData = new FormData; // フォーム方式で送る場
        postData.set('seminar_day', seminar_day); // set()で格納する

        console.log(...postData.entries());

        fetch('ajaxpctool/checkday', { // 第1引数に送り先
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, // CSRFトークン対策
            body: postData,
        })
        .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
        .then(res => {
            console.log(res); // やりたい処理
        })
        .catch(error => {
            console.log(error); // エラー表示
        });
    });

    
    //バックアップ

    // ajax_seminar_day.addEventListener('change', () => {
    //     console.log("イベント発火");
    // // 挿入した要素を取得
    // const insertedElement = document.querySelectorAll('.result');
    // // 要素を削除
    // for (let item of insertedElement) {
    //     item.remove();
    //     };

    //     let seminar_day = document.getElementById('ajax_seminar_day').value;
    //     let from = document.getElementById('ajax_from').value;
    //     let to = document.getElementById('ajax_to').value;

    //     if(seminar_day != null && from != null && to != null){
    //         console.log(seminar_day.value, from, to);
    //     }else{
    //         console.log('not input');
    //     }
    //    /*--------------------
    //         POST送信
    //     -------------------*/
    //     const postData = new FormData; // フォーム方式で送る場
    //     postData.set('seminar_day', document.getElementById('ajax_seminar_day').value); // set()で格納する
    //     postData.set('from', document.getElementById('ajax_from').value);
    //     postData.set('to', document.getElementById('ajax_to').value);

    //     console.log(...postData.entries());

    //     fetch('ajaxpctool/checkday', { // 第1引数に送り先
    //         method: 'POST',
    //         headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, // CSRFトークン対策
    //         body: postData,
    //     })
    //     .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
    //     .then(res => {
    //         console.log(res); // やりたい処理
    //     })
    //     .catch(error => {
    //         console.log(error); // エラー表示
    //     });
    // });


});