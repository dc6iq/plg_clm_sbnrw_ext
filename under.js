var plg_clm_sbnrw_content = new Array();

function plg_clm_sbnrw_modal_get(tid,round,urlbase,lang,number) {
  document.getElementById('plg_clm_sbnrw_'+number).innerHTML="";
  var xmlhttp;
  if (window.XMLHttpRequest) {
    xmlhttp=new XMLHttpRequest();
  } else {
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.open("GET", urlbase + "/plugins/content/plg_clm_sbnrw_ext/round.php?tid=" +
    tid + "&round=" + round + "&lang=" + lang, false);
  xmlhttp.send(null);

  if (xmlhttp.status === 200 && xmlhttp.responseText!="") {
    document.getElementById('plg_clm_sbnrw_'+number).innerHTML=xmlhttp.responseText;
    plg_clm_sbnrw_content[tid + "-" + round + "-" + lang] = xmlhttp.responseText;
  }
}

function plg_clm_sbnrw_modal_load (tid,round,urlbase,lang,number) {
  if(typeof plg_clm_sbnrw_content[tid + "-" + round + "-" + lang] != 'undefined') {
    document.getElementById('plg_clm_sbnrw_'+number).innerHTML=plg_clm_sbnrw_content[tid + "-" + round + "-" + lang];
  } else {
    plg_clm_sbnrw_modal_get(tid,round,urlbase,lang,number);
  }
}
