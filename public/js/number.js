const NUM = function(n){
    return n.replace(/[０-９]/g, function(s){
    return String.fromCharCode(s.charCodeAt(0) - 65248)
    }).replace(/\D/g,'');
    }
