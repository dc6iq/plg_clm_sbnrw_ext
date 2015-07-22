var plg_clm_sbnrw_content = new Array();

function plg_clm_sbnrw_modal_get(tid,round,urlbase,lang,number) {
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
    document.getElementById('plg-clm-sbnrw-overlay').innerHTML=xmlhttp.responseText;
    plg_clm_sbnrw_content[tid + "-" + round + "-" + lang] = xmlhttp.responseText;
  } else {
    plg_clm_sbnrw_modal_reload(tid,round,urlbase,lang);
  }
}

function plg_clm_sbnrw_modal_load (tid,round,urlbase,lang,number) {
	
	
  if(!document.getElementById("plg-clm-sbnrw-fade")) {
    document.getElementById('clm').innerHTML = '<div class="clm-wrapper"><div id="plg-clm-sbnrw-fade"></div><div id="plg-clm-sbnrw-overlay"><img class="load" src="' +
      urlbase + '/plugins/content/plg_clm_sbnrw_ext/ajax.gif" /></div><a onclick="plg_clm_sbnrw_modal_disable(\'' +
      urlbase + '\')" href="javascript:void(0)"><img id="plg-clm-sbnrw-close" src="' +
      urlbase + '/plugins/content/plg_clm_sbnrw_ext/close.png" /></a></div>' + document.getElementById('clm').innerHTML;
  }
  
  
  document.getElementById('plg-clm-sbnrw-fade').style.display='block';
  document.getElementById('plg-clm-sbnrw-overlay').style.display='block';
  document.getElementById('plg-clm-sbnrw-close').style.display='block';

  if(typeof plg_clm_sbnrw_content[tid + "-" + round + "-" + lang] != 'undefined') {
    document.getElementById('plg-clm-sbnrw-overlay').innerHTML=plg_clm_sbnrw_content[tid + "-" + round + "-" + lang];
  } else {
    plg_clm_sbnrw_modal_get(tid,round,urlbase,lang);
  }
}

function plg_clm_sbnrw_modal_reload(tid,round,urlbase,lang) {
  document.getElementById('plg-clm-sbnrw-overlay').innerHTML='<a onclick="plg_clm_sbnrw_modal_load(' +
    tid + ',' + round + ',\'' + urlbase + '\',\'' + lang +
    '\')" href="javascript:void(0)"><img class="load" src="' +
    urlbase +
    '/plugins/content/plg_clm_sbnrw_ext/reload.png" />';
}

function plg_clm_sbnrw_modal_disable(urlbase){
  document.getElementById('plg-clm-sbnrw-overlay').style.display='none';
  document.getElementById('plg-clm-sbnrw-fade').style.display='none';
  document.getElementById('plg-clm-sbnrw-close').style.display='none';
  document.getElementById('plg-clm-sbnrw-overlay').innerHTML='<img class="load" src="' +
    urlbase + '/plugins/content/plg_clm_sbnrw_ext/ajax.gif" />';
}
