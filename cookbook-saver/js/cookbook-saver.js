function requestCookbookDownload() {
  var data = {
    'action': 'download_cookbook'
  };
  jQuery.post(cookbook.ajax_url, data, function(cookbookFileContent) {
    var filename = 'MyCookbook.csv';
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(cookbookFileContent));
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
  });
}
