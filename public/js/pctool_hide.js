function $(id) {return document.getElementById(id)}
function $c(cl) {return document.getElementsByClassName(cl)}
function $n(name) {return document.getElementsByName(name)}
    function gray_on($class){
        for (var i=0; i< $c($class).length; i++){
            $c($class)[i].classList.add('gray');
            $c($class)[i].disabled = true;
            $c($class)[i].checked = false;
        }
    }
    function gray_off($class){
        for (var i=0; i< $c($class).length; i++){
            $c($class)[i].classList.remove('gray');
            $c($class)[i].disabled = false;
        }
    }
    window.addEventListener('DOMContentLoaded',function(){
        const show_used = document.getElementById('show_used'); // ①
        show_used.addEventListener('change',checksw,false);
        function checksw(){
            if( this.checked ){
                gray_on('chused');
                gray_on('trused');
            }else{
                gray_off('chused');
                gray_off('trused');

            }
        }
    });
