    window.addEventListener('DOMContentLoaded',function(){
        const show_finished = document.getElementById('show_finished'); // ①
        show_finished.addEventListener('change',checksw,false);
        function checksw(){
            if( this.checked ){
                document.getElementById('finishedlist').classList.remove('hidden');
            }else{
                document.getElementById('finishedlist').classList.add('hidden');
            }
        }
    });
